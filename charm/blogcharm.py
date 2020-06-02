#!/usr/bin/python3

import logging
import os
import shutil
import subprocess

from ops.charm import CharmBase
from ops.main import main
from ops.model import MaintenanceStatus, ActiveStatus

logger = logging.getLogger(__name__)

SYSTEM_DEPENDENCIES = ['git', 'python3-pip']

BASEDIR = '/var/lib'
BLOGDIR = os.path.join(BASEDIR, 'blog')


MAIN_DOMAIN = "taniqtest.com.ar"
VHOST_CONFIG = """
<VirtualHost *>
    ServerName blog.{main_domain}

    DocumentRoot {document_root}

    <Location />
        Require all granted
    </Location>

    AddDefaultCharset utf-8
    ServerSignature On
    LogLevel info

    ErrorLog  /var/log/apache2/blog-error.log
    CustomLog /var/log/apache2/blog-access.log combined
</VirtualHost>
"""


def run(cmd):
    """Execute a command, only showing output if error."""
    logger.info("Running command: %s", cmd)
    proc = subprocess.Popen(
        cmd, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, universal_newlines=True)

    for line in proc.stdout:
        logger.info(' :: %s', line.rstrip())

    retcode = proc.wait()
    if retcode:
        raise RuntimeError("Execution ended in {} for cmd {}".format(retcode, cmd))


class SuperCharm(CharmBase):

    def __init__(self, *args):
        super().__init__(*args)
        self.framework.observe(self.on.install, self.on_install)
        self.framework.observe(self.on.apache_website_relation_joined, self.on_apache_joined)
        self.framework.observe(self.on.upgrade_charm, self.on_upgrade)

    def _build_blog(self):
        """Build the blog."""
        logger.debug("nikola build")
        self.unit.status = MaintenanceStatus("Building the blog pages")
        blogsitedir = os.path.join(BLOGDIR, 'site')
        os.chdir(blogsitedir)
        run(['nikola', 'build'])

    def on_install(self, event):
        """Install everything needed."""
        logger.info("Installing blog...")

        logger.info("install! install system deps")
        self.unit.status = MaintenanceStatus("Installing system deps")
        run(['apt', 'install', '-y'] + SYSTEM_DEPENDENCIES)
        run(['locale-gen', 'es_AR.UTF-8'])

        # directory may be there even on install, as it's a subordinate charm (using an
        # already-there machine)
        if os.path.exists(BLOGDIR):
            shutil.rmtree(BLOGDIR)

        # get blog data
        logger.debug("install! clone project")
        self.unit.status = MaintenanceStatus("Cloning the project")
        run([
            'git', 'clone', '--depth', '1',
            'https://github.com/facundobatista/blog.git', BLOGDIR])

        # install project requirements
        logger.debug("install! install python deps")
        self.unit.status = MaintenanceStatus("Installing Python deps")
        requirements_path = os.path.join(BLOGDIR, 'requirements.txt')
        run(['pip3', 'install', '-r', requirements_path])

        # build the blog
        self._build_blog()

        # done!
        logger.info("Blog installed")
        self.unit.status = ActiveStatus()

    def on_upgrade(self, event):
        logger.info("============ upgrade!")

    def on_apache_joined(self, event):
        """We have apache, let's configure it."""
        logger.info("============ apache relation joined; event=%s", event)
        relation_data = event.relation.data[self.unit]
        logger.info("============ apache relation joined; data(self)=%s", dict(relation_data))

        # easy config
        relation_data['domain'] = MAIN_DOMAIN
        relation_data['enabled'] = 'true'
        relation_data['ports'] = '80'

        # the vhost configuration block
        blogpagesdir = os.path.join(BLOGDIR, 'site', 'output')
        site_config = VHOST_CONFIG.format(main_domain=MAIN_DOMAIN, document_root=blogpagesdir)
        print("========== site config", site_config)
        relation_data['site_config'] = site_config


if __name__ == '__main__':
    main(SuperCharm)

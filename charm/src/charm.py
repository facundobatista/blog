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


class BlogCharm(CharmBase):

    def __init__(self, *args):
        super().__init__(*args)
        self.framework.observe(self.on.install, self.on_install)
        self.framework.observe(self.on.apache_website_relation_joined, self.on_apache_joined)
        self.framework.observe(self.on.refresh_action, self.refresh)

    def _build_blog(self):
        """Build the blog."""
        self.unit.status = MaintenanceStatus("Building the blog pages")
        blogsitedir = os.path.join(BLOGDIR, 'site')
        os.chdir(blogsitedir)
        run(['nikola', 'build'])

    def on_install(self, event):
        """Install everything needed."""
        logger.info("Installing blog...")

        logger.info("Install system deps")
        self.unit.status = MaintenanceStatus("Installing system deps")
        # FIXME: we need to do this through "charmcraft system dependencies"
        run(['apt', 'install', '-y'] + SYSTEM_DEPENDENCIES)
        run(['locale-gen', 'es_AR.UTF-8'])

        # directory may be there even on install, as it's a subordinate charm (using an
        # already-there machine)
        if os.path.exists(BLOGDIR):
            shutil.rmtree(BLOGDIR)

        # get blog data
        logger.info("Cloning the blog project")
        self.unit.status = MaintenanceStatus("Cloning the project")
        run([
            'git', 'clone', '--depth', '1',
            'https://github.com/facundobatista/blog.git', BLOGDIR])

        # install project requirements
        logger.info("Install python deps")
        self.unit.status = MaintenanceStatus("Installing Python deps")
        requirements_path = os.path.join(BLOGDIR, 'requirements.txt')
        run(['pip3', 'install', '-r', requirements_path])

        # build the blog
        logger.info("Building the blog")
        self._build_blog()

        # done!
        logger.info("Blog installed")
        self.unit.status = ActiveStatus()

    def refresh(self, event):
        logger.info("Refresh! is leader %s", self.unit.is_leader)
        logger.info("Refresh! is leader() %s", self.unit.is_leader())
        event.set_results({ #FIXME
            'message': "Backup ended ok.",
            "time": 1361.77241278412983472378946237868,
        })
        return #FIXME

        self.unit.status = MaintenanceStatus("Refreshing blog")
        logger.info("Updating blog files")
        os.chdir(BLOGDIR)
        run(['git', 'pull'])

        logger.info("Rebuilding the blog")
        self._build_blog()

        #FIXME: log, set the active status and answer with the git hash
        git_hash = "FIXME"
        msg = "Blog refreshed to HEAD {}".format(git_hash)
        logger.info(msg)
        self.unit.status = ActiveStatus(msg)
        fail = False #FIXME
        if fail:
            event.fail("Device error: no space left")
        else:
            event.set_results({"HEAD hash": git_hash})

    def on_apache_joined(self, event):
        """We have apache, let's configure it."""
        relation_data = event.relation.data[self.unit]

        # easy config
        relation_data['domain'] = MAIN_DOMAIN
        relation_data['enabled'] = 'true'
        relation_data['ports'] = '80'

        # the vhost configuration block
        blogpagesdir = os.path.join(BLOGDIR, 'site', 'output')
        site_config = VHOST_CONFIG.format(main_domain=MAIN_DOMAIN, document_root=blogpagesdir)
        relation_data['site_config'] = site_config


if __name__ == '__main__':
    main(BlogCharm)

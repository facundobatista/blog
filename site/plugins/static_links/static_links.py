# -*- coding: utf-8 -*-

# Copyright María Andrea Vignau
#
# Permission is hereby granted, free of charge, to any
# person obtaining a copy of this software and associated
# documentation files (the "Software"), to deal in the
# Software without restriction, including without limitation
# the rights to use, copy, modify, merge, publish,
# distribute, sublicense, and/or sell copies of the
# Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice
# shall be included in all copies or substantial portions of
# the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY
# KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
# WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
# PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
# OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
# OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
# OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
# SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

"""Select one random propaganda image from a list. """

from nikola.plugin_categories import Task
from nikola import utils

import os
import os.path
import yaml  # depends on pyyaml

_LOGGER = utils.get_logger('render_static_links', utils.STDERR_HANDLER)


class StaticLinks(Task):
    """To render a static links html ."""

    def _gen_html(self, config, destination):
        """Generate html."""
        with open(config["input_file"], "r") as fb:
            data = yaml.load(fb.read())

        html_out = []

        for el in data:
            items = sorted(el[1], key=lambda links: links["data"])
            html_content = []
            for item in items:
                html_content.append(config["template_item"].format(**item))
            html_out.append(config["template_category"].format(
                title=el[0],
                content="\n".join(html_content)))

        with open(destination, "wt", encoding="utf8") as destination_file:
            destination_file.write(config["template"].format("\n".join(html_out)))

    def _gen_html_task(self, config):
        """Generate task to generate html."""
        destination = os.path.join(self.site.config['OUTPUT_FOLDER'], config["output_file"])
        changed = utils.config_changed(config, 'nikola.plugins.task.static_links')
        task = {
            'basename': self.name,
            'name': destination,
            'targets': [destination],
            'actions': [(self._gen_html, [config, destination])],
            'clean': True,
            'uptodate': [changed, destination]
        }
        return task

    def gen_tasks(self):
        """Generate task."""

        config = self.site.config['STATIC_LINKS']
        filters = self.site.config['FILTERS']

        # generate html to insert into sidebar

        task = self._gen_html_task(config)
        task['uptodate'] = [utils.config_changed(config, 'nikola.plugins.task.static_links')]
        yield utils.apply_filters(task, filters)

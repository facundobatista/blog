# -*- coding: utf-8 -*-

# Copyright © 2014-2017 Felix Fontein
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
from plugins.propaganda.variables import html

_LOGGER = utils.get_logger('render_propaganda', utils.STDERR_HANDLER)



class Propaganda(Task):
    """To render an propaganda box with content of some defined subdir."""

    def _gen_html(self, destination, images_folder, conf):
        """Generate html."""
        list_item = """images[{item}] = ["{image}", "{url}", "{title}"];"""

        n = 0
        items = []
        for item in os.listdir(images_folder):
            item_path = os.path.join(images_folder, item)
            if os.path.isdir(item_path):

                with open(os.path.join(item_path, 'url.txt'), 'rt') as url_file:
                    title, url = url_file.readlines()

                propaganda = {
                    'item': n,
                    'title': title.strip(),
                    'url': url.strip(),
                    'image': os.path.join(item, conf['image_name'])
                }
                n += 1
                items.append(list_item.format(**propaganda))
        conf["propaganda_list"] = "\n".join(items)

        local_html = html
        for key, value in conf.items():
            local_html = local_html.replace("{{%s}}" % key, str(value))

        with open(destination, "wt", encoding="utf8") as destination_file:
            destination_file.write(local_html)

    def _gen_html_task(self, images_folder, conf, key):
        """Generate task to generate html."""
        destination = os.path.join(self.site.config['OUTPUT_FOLDER'], conf[key]['generated_at'])
        conf[key]['key'] = key
        task = {
            'basename': self.name,
            'name': destination,
            'targets': [destination],
            'actions': [(self._gen_html, [destination, images_folder, conf[key]])],
            'clean': True,
            'uptodate': [utils.config_changed(conf, 'nikola.plugins.task.propaganda'), destination]
        }
        return task



    def gen_tasks(self):
        """Generate tasks."""

        conf = self.site.config['PROPAGANDA']
        images_folder = os.path.join(self.site.config['OUTPUT_FOLDER'], 'propaganda')
        filters = self.site.config['FILTERS']

        # copy files to static site
        for task in utils.copy_tree(conf['source_folder'], images_folder, link_cutoff=images_folder):
            task['basename'] = self.name
            task['uptodate'] = [utils.config_changed(conf, 'nikola.plugins.task.propaganda')]
            yield utils.apply_filters(task, filters, skip_ext=['.html'])

        conf["items"] = os.listdir(conf['source_folder'])

        # generate html to insert into sidebar

        for key in conf.keys():
            if key.startswith("task"):
                task = self._gen_html_task(images_folder, conf, key)
                yield utils.apply_filters(task, filters)



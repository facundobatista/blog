# Copyright 2018 María Andrea Vignau
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

"""Render an archive month list and a box with a time ago post to be included in sidebar."""

from nikola.plugin_categories import Task
from nikola import utils

import os
import os.path

_LOGGER = utils.get_logger('render_archive_bar', utils.STDERR_HANDLER)

_MONTH_NAMES = {
    1: "enero",
    2: "febrero",
    3: "marzo",
    4: "abril",
    5: "mayo",
    6: "junio",
    7: "julio",
    8: "agosto",
    9: "septiembre",
    10: "octubre",
    11: "noviembre",
    12: "diciembre",
}


class Month_Page(object):
    '''Define a month page'''
    def __init__(self, friendly_name, item):
        self.friendly_name = friendly_name
        self.year, self.month = [part for part in item.split('/')]


class RenderArchiveBar(Task):
    """Render an archive bar."""

    name = "render_archive_bar"

    def set_site(self, site):
        """Set site."""
        super(RenderArchiveBar, self).set_site(site)

    def _build_month_post_list(self):
        """Create a list of months."""
        try:
            months = self.site.posts_per_month.keys()
            months = sorted(months, reverse=True)
            month_list = []
            for item in months:
                year, month = [int(part) for part in item.split('/')]
                month_name = _MONTH_NAMES[month]
                month_page = Month_Page("{} {}".format(month_name, year), item)
                month_list.append(month_page)

            return month_list
        except KeyError:
            return None

    def _prepare_task(self, destination, lang, template):
        """Generates the sidebar task for the given language."""
        context = {}
        deps_dict = {}

        month_list = self._build_month_post_list()
        context['month_list'] = month_list
        deps_dict['month_list'] = [
            (month.year + '/' + month.month, month.friendly_name)
            for month in month_list]

        task = self.site.generic_renderer(lang,
                                          destination,
                                          template,
                                          self.site.config['FILTERS'],
                                          context=context,
                                          post_deps_dict=deps_dict,
                                          url_type=self.site.config['URL_TYPE'],
                                          is_fragment=True)
        task['basename'] = self.name
        yield task

    def gen_tasks(self):
        """Generate tasks."""
        self.site.scan_posts()

        for lang in self.site.config['TRANSLATIONS'].keys():
            destination = os.path.join(
                self.site.config['OUTPUT_FOLDER'],
                'archive_bar-{0}.html'.format(lang))
            template = 'archives_bar.tmpl'
            yield self._prepare_task(destination, lang, template)

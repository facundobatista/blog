# -*- coding: utf-8 -*-

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
import datetime

_LOGGER = utils.get_logger('render_archive_bar', utils.STDERR_HANDLER)
class Month_Page(object):
    '''Define a month page'''
    def __init__(self, friendly_name, item):
        self.friendly_name = friendly_name
        self.year, self.month = [part for part in item.split('/')]

class RenderSidebar(Task):
    """Render a sidebar."""

    name = "render_sidebar"

    def set_site(self, site):
        """Set site."""
        super(RenderSidebar, self).set_site(site)

    def _post_time_ago(self, timedelta):
        """Select a post that was publish a `timedelta` ago, or last post on other case."""
        posts = sorted(self.site.posts, key=lambda post: post.date, reverse=True)
        target_date = datetime.datetime.now(datetime.timezone.utc) - timedelta
        for post in posts:
            if post.date <= target_date:
                return post
        return post    # return last post in other case

    def _build_month_post_list(self, lang):
        """Create a list of months."""
        try:
            months = list(self.site.posts_per_month.keys())
            months = sorted(months, reverse=True)
            month_list = []
            for item in months:
                year, month = [int(part) for part in item.split('/')]
                month_name = utils.LocaleBorg().get_month_name(month, lang)
                month_page = Month_Page("{} {}".format(month_name, year), item)
                month_list.append(month_page)

            return month_list
        except KeyError:
            return None


    def _prepare_sidebar(self, destination, lang, template):
        """Generates the sidebar task for the given language."""
        context = {}
        deps_dict = {}

        month_list = self._build_month_post_list(lang)
        context['month_list'] = month_list
        deps_dict['month_list'] = [(month.year + '/' + month.month, month.friendly_name) for month in month_list]

        year_ago_post = self._post_time_ago(datetime.timedelta(days=365))
        context['year_ago_post'] = year_ago_post

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
        yield self.group_task()

        for lang in self.site.config['TRANSLATIONS'].keys():
            destination = os.path.join(self.site.config['OUTPUT_FOLDER'], 'archive_bar-{0}.html'.format(lang))
            template = 'archives_bar.tmpl'
            yield self._prepare_sidebar(destination, lang, template)

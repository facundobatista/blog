# -*- coding: utf-8 -*-

# Copyright © 2014 Puneeth Chaganti

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

from __future__ import print_function, unicode_literals

from nikola.plugin_categories import LateTask
from nikola.utils import config_changed, LOGGER


class RenderPosts(LateTask):
    """ Check warnings on any post that may have changed. """

    name = 'check_warnings'

    def __init__(self):
        super(RenderPosts, self).__init__()
        self._dicts = dict()

    def gen_tasks(self):
        """ Check warnings on any post that may have changed. """

        self.site.scan_posts()
        kw = {'translations': self.site.config['TRANSLATIONS']}
        yield self.group_task()

        for lang in kw['translations']:
            for post in self.site.timeline[:]:
                path = post.fragment_deps(lang)
                task = {
                    'basename': self.name,
                    'name': path,
                    'file_dep': path,
                    'actions': [(self.check_warnings, (post, lang, ))],
                    'clean': True,
                    'uptodate': [config_changed(kw)],
                }
                yield task

    def check_warnings(self, post, lang):
        """ Check warnings for the given post and given language. """
        text = post.text(lang=lang)
        if 'class="system-message"' in text:
            LOGGER.notice(
                'Warnings in %s' % (
                    post.fragment_deps(lang)
                )
            )


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

"""Create a slideshow ."""

from nikola.plugin_categories import Task
from nikola import utils

import natsort
import os
import os.path
import datetime

_LOGGER = utils.get_logger('render_sidebar', utils.STDERR_HANDLER)



class Advertising(Task):
    """"""

    def _gen_html(self, destination, images_folder):
         list_item = """
         <li id="{item}" class="hideable" {style}>
            <a href="{url}">
                <img src="/propaganda/{image}" alt="{title}" />
            </a></li>
         """
         with open(destination, "wb") as destination_file:
            destination_file.write(html.encode('utf-8'))
            n = 1
            style = 'style="display: block;"'
            for item in os.listdir(images_folder):
                item_path = os.path.join(images_folder, item)
                if os.path.isdir(item_path):

                    with open(os.path.join(item_path, 'url.txt'), 'r') as url_file:
                        title, url = url_file.readlines()

                    advertise = {
                        'item': n,
                        'style': style,
                        'title': title.strip(),
                        'url': url.strip(),
                        'image': os.path.join(item, 'logo_c.png')
                    }
                    n += 1
                    style = ''
                    other_item = list_item.format(**advertise).encode('utf-8')
                    destination_file.write(other_item)

            destination_file.write('</ul>'.encode('utf-8'))




    def gen_tasks(self):
        """Generate tasks."""

        destination = os.path.join(self.site.config['OUTPUT_FOLDER'], 'advertising.html')
        images_folder = os.path.join(self.site.config['OUTPUT_FOLDER'], 'propaganda')

        task = {
            'basename': self.name,
            'name': destination,
            'targets': [destination],
            'actions': [(self._gen_html, [destination, images_folder])],
            'clean': True,
            'uptodate': []
        }

        yield task

html = """

<style type="text/css">
.hideable {
    display: none;
}
img {
    width: 180px;
    height: 120px;
}
</style>
            <base href="propaganda/" />

<script type="text/javascript">
// direction = boolean value: true or false. If true, go to NEXT slide; otherwise go to PREV slide
function toggleSlide() {
    var elements = document.getElementsByClassName("hideable"); // gets all the "slides" in our slideshow

    // Find the LI that's currently displayed
    var visibleID = getVisible(elements);

    elements[visibleID].style.display = "none"; // hide the currently visible LI
    var makeVisible = next(visibleID, elements.length); // get the next slide
    elements[makeVisible].style.display = "block"; // show the next slide
    var sn = document.getElementById("slideNumber");
    sn.innerHTML = (makeVisible + 1);
}

function getVisible(elements) {
    var visibleID = -1;
    for(var i = 0; i < elements.length; i++) {
        if(elements[i].style.display == "block") {
            visibleID = i;
        }
    }
    return visibleID;
}

function next(num, arrayLength) {
    if(num == arrayLength-1) return 0;
    else return num+1;
}

var interval = 1000; // You can change this value to your desired speed. The value is in milliseconds, so if you want to advance a slide every 5 seconds, set this to 5000.
var switching = setInterval("toggleSlide()", interval);
</script>

                <ul style="list-style-type:none; margin-left:-2em;">
        """
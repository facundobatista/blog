This plugin renders`output/archive_bar-LANG.html` defined by a template `archive_bar.tmpl`. 

The archive bar can contain a list of selectable month archive indexes and a box with a post published a year ago. 

The packaged `archive_bar.tmpl` and `archive_bar-helper.tmpl` shows how this information can be shown.

The generated include file, one per language, must be somehow included in the rest of the blog. This can be done by using JavaScript to dynamically include the file, or by using tools like [File Tree Subs](https://github.com/felixfontein/filetreesubs/).


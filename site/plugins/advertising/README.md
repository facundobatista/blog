Select one random advertisment image from a list.

This plugin renders an advertising box, selecting from a directory folder.

The generated include file, must be somehow included in the rest of the blog. This can be done by using JavaScript to dynamically include the file, or by using tools like [File Tree Subs](https://github.com/felixfontein/filetreesubs/).

It must be configured on conf.py like this:

ADVERTISING = {

    "source_folder": "../propaganda",

    "image_width": 180,

    "image_heigth": 120

}

In source folder there should be one subdirectory for every advertise, which must have at least two files: one named logo_c.png with the advertise image and one called url.txt with the alternative name of image on its first line and the url referenced on the second line.

propaganda
 |
 +--advertisment A
 |  |
 |  +-- logo_c.png
 |  |
 |  +-- url.txt
 |
 +--advertisment B
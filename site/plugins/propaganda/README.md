Select one random propaganda image from a list.

This plugin renders an propaganda box, selecting from a directory folder.

The generated include file, must be somehow included in the rest of the blog. This can be done by using JavaScript to dynamically include the file, or by using tools like [File Tree Subs](https://github.com/felixfontein/filetreesubs/).

It must be configured on conf.py like this:

```python
PROPAGANDA = {

    "source_folder": "../propaganda",    
    "tasks": {
    "prop_c": {
        "image_width": 180,
        "image_heigth": 120,
        "image_name": "logo_c.png",
        "generated_at": "propaganda_c.html",
    },
    "prop_r": {
        "image_width": 460,
        "image_heigth": 60,
        "image_name": "logo_r.png",
        "generated_at": "propaganda_r.html",
    }
    }
}
```
The names of generated html are *propaganda_c.html* and *propaganda_r.html*
In source folder there should be one subdirectory for every propaganda, which must have at least three files: one named logo_c.png, one named logo_r with the propaganda image and one called url.txt with the alternative name of image on its first line and the url referenced on the second line.

```
propaganda
 |
 +--propaganda A
 |  |
 |  +-- logo_c.png
 |  |
 |  +-- logo_r.png
 |  |
 |  +-- url.txt
 |
 +--propaganda B
 ```
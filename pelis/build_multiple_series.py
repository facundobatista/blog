#!/usr/bin/fades

"""Build a sequence of images one side to the other."""

import os
import sys

from PIL import Image  # fades


def main(out_fname, image_paths):
    """Main entry point."""
    images = []
    heights = set()
    for path in image_paths:
        image = Image.open(path)
        images.append(image)
        heights.add(image.height)

    if len(heights) > 1:
        raise ValueError(f"Error: multiple heights: {heights}")
    src_height = heights.pop()

    # the separator is a tenth of widths average
    all_widths = sum(image.width for image in images)
    sep_width = int((all_widths / len(images)) / 10)

    # the width of all images, plus the separators between images and borders
    final_canvas_width = all_widths + (len(images) - 1) * sep_width

    canvas = Image.new("RGB", (final_canvas_width, src_height), (255, 255, 255))

    start_y = start_x = 0
    for image in images:
        canvas.paste(image, (start_x, start_y))
        start_x += image.width + sep_width

    canvas.save(out_fname)


if len(sys.argv) < 4:
    print("Usage: build_multiple_series.py outname img1 img2 [img3 [...]]")
    print("          to avoid overwriting an image by error, outname must not exist")
    exit()

out_fname = sys.argv[1]
if os.path.exists(out_fname):
    print(f"ERROR: output file {out_fname} already exists")
    exit()
image_paths = sys.argv[2:]
main(out_fname, image_paths)

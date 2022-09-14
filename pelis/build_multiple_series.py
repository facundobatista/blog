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
    (src_height,) = heights

    # the separator is a tenth of widths average (no matter which row)
    all_widths = sum(image.width for image in images)
    separator = int((all_widths / len(images)) / 10)

    # two rows if too many images
    if len(images) > 5:
        final_canvas_height = src_height * 2 + separator
        row1_images = images[:len(images) // 2]
        row2_images = images[len(row1_images):]
    else:
        final_canvas_height = src_height
        row1_images = images
        row2_images = []
    # print("=========== rows", len(row1_images), len(row2_images))

    # the width of all images, plus the separators between images and borders
    row1_widths = sum(image.width for image in row1_images) + (len(row1_images) - 1) * separator
    row2_widths = sum(image.width for image in row2_images) + (len(row2_images) - 1) * separator
    final_canvas_width = max(row1_widths, row2_widths)
    # print("=========== FCW", final_canvas_width)

    canvas = Image.new("RGB", (final_canvas_width, final_canvas_height), (255, 255, 255))

    # row 1
    start_x = (final_canvas_width - row1_widths) // 2
    # print("========== r1 startx", start_x)
    start_y = 0
    for image in row1_images:
        canvas.paste(image, (start_x, start_y))
        start_x += image.width + separator

    # row 2
    start_x = (final_canvas_width - row2_widths) // 2
    # print("========== r2 startx", start_x)
    start_y += src_height + separator
    for image in row2_images:
        canvas.paste(image, (start_x, start_y))
        start_x += image.width + separator

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

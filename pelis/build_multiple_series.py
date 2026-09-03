#!/usr/bin/fades

"""Build a sequence of images one side to the other."""

import math
import os
import sys

from PIL import Image  # fades


def main(out_fname, image_paths):
    """Main entry point."""
    images = []
    for path in image_paths:
        image = Image.open(path)
        images.append(image)

    # the separator is a tenth of widths average (no matter which row)
    all_widths = sum(image.width for image in images)
    separator = int((all_widths / len(images)) / 10)
    print("========= sep", separator)

    # two rows if too many images
    rows_count = math.ceil(len(images) / 5)
    base, extra = divmod(len(images), rows_count)
    row_img_q = [base + (i < extra) for i in range(rows_count)]
    print("=========== rows", row_img_q)

    # the width of all images, plus the separators between images and borders
    row_widths = []
    row_heights = []
    each_row_images = []
    ini = 0
    end = 0
    for jump in row_img_q:
        end += jump
        # the images on this row, store them for later
        row_images = images[ini:end]
        ini = end
        each_row_images.append(row_images)

        # get width (total) and height (max) for the row layout
        row_width = sum(image.width for image in row_images) + (len(row_images) - 1) * separator
        row_widths.append(row_width)
        row_height = max(image.height for image in row_images)
        row_heights.append(row_height)

    # the final canvas size is the max width and the sum of heights + separators
    print("========== rws??", row_widths)
    print("========== rhs??", row_heights)
    final_canvas_width = max(row_widths) + separator * 2
    final_canvas_height = sum(row_heights) + (rows_count - 1) * separator + separator * 2
    print("=========== FC size", final_canvas_width, final_canvas_width)

    canvas = Image.new("RGB", (final_canvas_width, final_canvas_height), (255, 255, 255))

    # row by row
    row_base_y = separator
    for row_width, row_height, row_images in zip(row_widths, row_heights, each_row_images):

        start_x = (final_canvas_width - row_width) // 2
        for image in row_images:
            img_y = row_base_y + (row_height - image.height) // 2
            canvas.paste(image, (start_x, img_y))
            start_x += image.width + separator

        row_base_y += row_height + separator

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

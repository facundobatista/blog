"""
To convert txt to rst, converting the first three lines
in Nikola metadata format, and replacing duplicated tags

"""

import os
import re

path_source = "./done"
path_dest = "../site/posts"
if not os.path.exists(path_dest):
    os.mkdir(path_dest)


fields = ["date", "title", "tags"]


def main():
    """Loop through all post in 'done' folder."""
    total = {True: 0, False: 0}
    for filename in os.listdir(path_source):
        try:
            file_out = check_file(filename)
        except ValueError as err:
            print(err)
            total[False] += 1
            continue
        process_file(filename, file_out)
        total[True] += 1
    print("\nFiles: {} processed, {} not processed".format(total[True], total[False]))


def check_file(filename):
    """Check if file will be processed.

    Return file_out name or error message.
    """
    file_split = filename.rsplit(".", 1)
    if not len(file_split) > 1:
        raise ValueError(
            "File '{}' doesn't have file extension and won't be processed.".format(filename))
    if file_split[1] != 'txt':
        raise ValueError(
            "File '{}' doesn't have 'txt' extension and won't be processed.".format(filename))
    file_out = os.path.join(path_dest, file_split[0] + ".rst")
    return file_out


def get_tags_from_title(title):
    words = re.split("\W+", title)
    words = [w for w in words if len(w) > 1]
    words = [w.lower() for w in words if len(w) > 2 or w[0].isupper()]
    return words


def process_file(filename, file_out):
    """Add metadata fields to header."""
    # load tag translations
    with open('fix_tags.txt', 'rt', encoding='utf8') as fh:
        tag_trans = dict(line.strip().split(" ") for line in fh)

    with open(os.path.join(path_source, filename), "rt", encoding="utf8") as fin:
        all_lines = fin.readlines()
        date, title, tags, *body = all_lines

        # remove empty lines at the beginning, and at the end
        while not body[0].strip():
            del body[0]
        while not body[-1].strip():
            del body[-1]

        body = "".join(body)

        if tags.strip() == "<tags>":
            tags = get_tags_from_title(title)
        else:
            tags = [t.strip() for t in tags.split(",")]

        # translate the tags
        tags = [tag_trans.get(t, t) for t in tags]

        with open(file_out, "wt", encoding="utf8") as fout:
            fout.write('.. title: {}'.format(title))
            fout.write('.. date: {}'.format(date))
            fout.write('.. tags: {}'.format(", ".join(tags)))
            fout.write('\n\n')
            fout.write(body)


if __name__ == "__main__":
    main()

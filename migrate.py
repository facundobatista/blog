"""
To convert txt to rst, converting the first three lines
in Nikola metadata format, and replacing duplicated tags


"""

import os

path_source = "./migrac/done"
path_dest = "./site/posts"
if not os.path.exists(path_dest):
    os.mkdir(path_dest)


fields = ["date", "title", "tags"]


def main():
    """Loop through all post in 'done' folder."""
    total = {True: 0, False: 0}
    for filename in os.listdir(path_source):
        error, output = check_file(filename)
        if error:
            print(output)
        else:
            process_file(filename, output)
        total[error] += 1
    print("\nFiles: {} processed, {} not processed".format(total[False], total[True]))


def check_file(filename):
    """Check if file will be processed.
    Return file_out name or error message."""
    file_split = filename.rsplit(".", 1)
    if not len(file_split) > 1:
        return True, "File '{}' doesn't have file extension and won't be processed.".format(filename)
    if file_split[1] != 'txt':
        return True, "File '{}' doesn't have 'txt' extension and won't be processed.".format(filename)
    file_out = os.path.join(path_dest, file_split[0] + ".rst")
    if os.path.exists(file_out):
        return True, "File '{}' exists and won't be overwritten.".format(file_out)
    else:
        return False, file_out


def process_file(filename, file_out):
    """Add metadata fields to header."""
    with open(os.path.join(path_source, filename), "rt", encoding="utf8") as fin:
        with open(file_out, "wt", encoding="utf8") as fout:
            for nline, line in enumerate(fin.readlines()):
                if nline < len(fields):
                    line = '.. %s: %s' % (fields[nline], line)
                fout.write(line)


if __name__ == "__main__":
    main()

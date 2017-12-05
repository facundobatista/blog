"""

"""

import os

path_source = "./migrac/done"
path_dest = "./site/posts"
if not os.path.exists(path_dest):
    os.mkdir(path_dest)


fields = ["date", "title", "tags"]

replacements = [("pinchilon", "pinchilón"),
                ("<tags>" , "tags"),
                ("imagenes", "imágenes"),
                ("santa fe", "Santa Fé"),
                ("santa fé", "Santa Fé"),
                ("córdoba", "Córdoba"),
                ("holanda", "Holanda"),
                ("reunion", "reunión"),
                ("utrecht", "Utrecht"),
                ("u2", "U2")
]

def main():
    """Loop through all post in 'done' folder """
    for file_in in os.listdir(path_source):
        progress()
        if file_in.endswith("txt"):
            with open(os.path.join(path_source, file_in), "rt+", encoding="utf8") as fin:
                file_out = os.path.join(path_dest, file_in.split(".", -1)[0] + ".rst")
                if not os.path.exists(file_out):
                    with open(file_out, "wt+", encoding="utf8") as fout:
                        process_file(fin, fout)

def process_file(fin, fout):
    """Add yaml fields to header and fix some tags"""
    for nline, line in enumerate(fin.readlines()):
        if nline < len(fields):
            if fields[nline] == "tags":
                for rep in replacements:
                    line = line.replace(rep[0], rep[1])
            line = '.. %s: %s' % (fields[nline], line)
        fout.write(line)

n = 0
def progress():
    """ Show something while run process"""
    global n
    n += 1
    if n % 70 == 0:
        print(".")
    else:
        print(".", end="")

if __name__ == "__main__":
    main()
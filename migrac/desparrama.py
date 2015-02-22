

def process(bloque):
    header, *resto = bloque
    hparts = header.split("|")
    print("====== P", hparts)

    postnum = hparts[1].strip()
    dt = hparts[2].strip()
    title = hparts[5].strip()

    fname = postnum.zfill(4) + ".txt"

    with open(fname, "wt", encoding='utf8') as fh:
        fh.write(dt + "\n")
        fh.write(title + "\n")
        fh.write("<tags>\n\n")
        fh.writelines(resto)


with open("lista.txt", "rt", encoding='utf8') as fh:
    bloque = []
    for line in fh:
        if "|" == line[0]:
            if bloque:
                process(bloque)
            bloque = []
        bloque.append(line)
    process(bloque)

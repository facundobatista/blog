# select article_id, date, topic, normalized_text from plog_articles inner join plog_articles_text using(id) where article_id = 641;


def process(bloque):
    header, *resto = bloque
    hparts = header.split("|")

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

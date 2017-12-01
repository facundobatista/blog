# select article_id, date, topic, text from plog_articles inner join plog_articles_text using(id) where article_id > 667;

def process(bloque):
    print("====== bloque", bloque)
    firstline, *resto = bloque
    _, postnum, dt, title, text_line1 = [x.strip() for x in firstline.split("|")]
    fname = postnum.zfill(4) + ".txt"

    with open(fname, "wt", encoding='utf8') as fh:
        fh.write(dt + "\n")
        fh.write(title + "\n")
        fh.write("<tags>\n\n")
        fh.write(text_line1 + "\n")
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

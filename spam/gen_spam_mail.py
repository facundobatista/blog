#!/usr/bin/python3

import glob
import re
import sys

LINK = "https://blog.taniquetil.com.ar/posts/{post_number}/"
LINES = 3
WIDTH = 60

HEADER = """
¡Buenas, gente!

Les dejo el resumen de mi blog para el mes pasado...

"""

FOOTER = """
Espero que lo disfruten, y que sigan bien.

Y como siempre, si quieren dejar de recibir esto, sólo me avisan y ya, no importa el motivo, no pasa nada.

¡Saludos!
"""  # NOQA


def clean(text):
    text = re.sub("<.*?>", "", text)
    text = text.replace("`_", "")
    text = text.replace("`", "")
    return text


def build_paragrap(txt, pref, maxwidth, maxlines):
    txt = txt.replace(".", ". ")
    txt = txt.replace("  ", " ")
    palabras = txt.split()              # y separamos las palabras

    # armamos las lineas
    lineas = []
    lin = [pref]
    ccar = len(pref)
    clin = 0
    for pal in palabras:
        ccar += len(pal) + 1
        if ccar > maxwidth:
            lineas.append(lin)
            lin = [pref]
            ccar = len(pref) + len(pal) + 1
            clin += 1
            if clin == maxlines:
                break
        lin.append(pal)

    # agregamos "..." en la última linea
    if not lineas:
        return ""
    linprim = lineas[:-1]
    linult = lineas[-1]
    # si se pasa del largo, boleteamos la ult palabra
    if len(" ".join(linult)) + 3 > maxwidth:
        linult = linult[:-1]
    # le pegamos los tres puntos si ya no los tiene
    if linult[-1][-3:] != "...":
        linult[-1] = linult[-1] + "..."
    lineas = linprim + [linult]

    # juntamos todo
    lineas = [' '.join(x) for x in lineas]
    return '\n'.join(lineas)


def main(filter_ym):
    posts = glob.glob("../site/posts/????.rst")

    inmonth = False
    content = []
    for post_path in sorted(posts, reverse=True):
        with open(post_path, "rt", encoding="utf8") as fh:
            title_raw = fh.readline()
            date_raw = fh.readline()
            assert date_raw.startswith(".. date:")
            y_m_d = date_raw.split()[2]
            post_ym = y_m_d[:4] + y_m_d[5:7]
            post_number = post_path.split('/')[-1].split('.')[0]
            if post_ym != filter_ym:
                if inmonth:
                    # already processed the filtered that we wanted
                    break
                else:
                    # didn't get to the month we want yet
                    continue

            inmonth = True

            assert title_raw.startswith(".. title:")
            title = title_raw[10:].strip()

            # discard tags
            assert fh.readline().startswith(".. tags:")

            text = clean(fh.read())
            content.append((post_number, title, text))

    if not content:
        print("Nothing found!")
        sys.exit()

    content = reversed(content)

    arch = open("mail.txt", "w")
    arch.write(HEADER)
    separator = "-" * WIDTH + "\n"
    for post_number, title, text in content:
        txt = build_paragrap(text, "  ", WIDTH, LINES)
        link = LINK.format(post_number=post_number)

        # lo grabamos
        arch.write(separator)
        arch.write("\n")
        arch.write(title + "\n")
        arch.write("\n")
        arch.write(txt + "\n")
        arch.write("\n")
        arch.write(link + "\n")
        arch.write("\n")

    arch.write(separator)
    arch.write(FOOTER)
    arch.close()
    return


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usar: gen_spam_mail.py YYYYMM")
        sys.exit()
    yearmonth = sys.argv[1]
    if len(yearmonth) != 6 or not yearmonth.isdigit():
        print("Usar: gen_spam_mail.py YYYYMM")
        sys.exit()
    main(yearmonth)

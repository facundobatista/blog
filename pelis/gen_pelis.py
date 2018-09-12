#!/usr/bin/fades

import collections
import datetime
import locale
import operator
import os
import pickle
import re
import sys

from imdb import IMDb  # fades IMDbPy


T_REVIEWS_HEAD = """\
FIXME: headers!!!

FIXME: Texto inicial acá

Otra linea, etc
"""
T_REVIEWS_BODY = '- `{title} <{urlimdb}>`_: {vote:}. {explanation:}'

RE_REVIEW_LINE = '(.*?): ([+-?][\d?]). (.*)'

T_FUTURES_HEAD = """\
FIXME: Mas peliculas anotadas para ver:

Otra linea, etc
"""
T_FUTURES_BODY = '- `{title} <{urlimdb}>`_: {description}'

RE_PELIS_LINE = '<a href="(.*?)">(.*?)</a> <font size="-2"><i>(.*?)</i></font><br>'
T_PELIS_LINE = '<a href="{urlimdb:}">{title:}</a> <font size="-2"><i>{date:}</i></font><br>'

locale.setlocale(locale.LC_ALL, "es_AR.UTF8")
NEW_DATE = datetime.date.today().strftime("(%b-%Y)").title()

T_DATECOUNT_HEAD = "Finalmente, el conteo de pendientes por fecha::\n"""
T_DATECOUNT_TAIL = "\n"


class Cache(object):
    """An automatic cache in disk."""
    def __init__(self, fname):
        self.fname = fname
        self.db = None

    def _init(self):
        """Init the cache DB."""
        if os.path.exists(self.fname):
            with open(self.fname, "rb") as fh:
                self.db = pickle.load(fh)
        else:
            self.db = {}
        print("DB loaded, len", len(self.db))

    def get(self, key):
        """Return a value in the DB."""
        if self.db is None:
            self._init()
        return self.db[key]

    def set(self, key, value):
        """Set a value to the DB."""
        if self.db is None:
            self._init()
        self.db[key] = value
        temp = self.fname + ".tmp"
        with open(temp, "wb") as fh:
            pickle.dump(self.db, fh)
        os.rename(temp, self.fname)


cache = Cache("/home/facundo/.cache/collect_movie_info.pkl")


class IMDB(object):
    def __init__(self):
        self.imdb = IMDb()

    def get_movie(self, movie_id):
        """Get a movie."""
        try:
            m = cache.get(movie_id)
        except KeyError:
            m = self.imdb.get_movie(movie_id)
            cache.set(movie_id, m)
        return m


imdb = IMDB()


def _fix_urlimdb(url):
    """Fix IMDB url."""
    assert url

    # add a trailing slash if doesn't have one
    if not url.endswith("/"):
        url += "/"

    # clean extra words
    if url.endswith('combined/'):
        url = url[:-9]
    if url.endswith('reference/'):
        url = url[:-10]
    url = url.split("?ref_")[0]

    # return the url and movie_id separated
    ttid = url.split("/")[4]
    assert ttid[:2] == 'tt'
    movie_id = ttid[2:]
    return url, movie_id


def process_reviews(reviews):
    data = []
    viewed = set()
    for revline, urlimdb, movie_id in reviews:
        viewed.add(urlimdb)
        m = re.match(RE_REVIEW_LINE, revline)
        if not m:
            print("ERROR: review line not recognized:", repr(revline))
            exit()
        annot_title, vote, explanation = m.groups()
        movie = imdb.get_movie(movie_id)
        real_title = movie['title']
        print("Processing review ({}): {}".format(annot_title, real_title))
        datum = dict(title=real_title, vote=vote,
                     explanation=explanation, urlimdb=urlimdb)
        data.append(datum)
    data.sort(key=operator.itemgetter('title'))

    resp = [T_REVIEWS_HEAD]
    for datum in data:
        resp.append(T_REVIEWS_BODY.format(**datum))
    return resp, viewed


def process_futures(futures):
    resp = [T_FUTURES_HEAD]
    for annot_title, urlimdb, movie_id in sorted(futures):
        movie = imdb.get_movie(movie_id)

        directors = ", ".join(d['name'] for d in movie.get('director', []))
        actors = ", ".join(a['name'] for a in movie['actors'][:3])
        genres = ", ".join(movie['genres'])
        plot = movie.get('plot', ["-"])[0]
        desc = "({}; {}) {} [D: {}; A: {}]".format(
            movie['year'], genres, plot, directors, actors)

        real_title = movie['title']
        print("Processing futures ({}): {}".format(annot_title, real_title))
        resp.append(T_FUTURES_BODY.format(title=real_title, urlimdb=urlimdb,
                                          description=desc))
    return resp


def proc_pelshtml(futures, viewed):
    """Process peliculas.html and generate some results."""
    # read peliculas.html
    movies = []
    datecount = []
    dc_useful = False
    with open("../bdv/peliculas.html", 'rt', encoding='utf8') as fh:
        for line in fh:
            line = line.strip()

            m = re.match(RE_PELIS_LINE, line)
            if m:
                urlimdb, title, date = m.groups()
                urlimdb = urlimdb.replace('http://', 'https://')
                if urlimdb in viewed:
                    # clean it in viewed
                    viewed.remove(urlimdb)
                else:
                    # not viewed, append to the list of movies to remain
                    movies.append((title.strip(), urlimdb, date))

            if dc_useful:
                if line == '<!-- Date count end -->':
                    dc_useful = False
                    continue
                if line[0] == '(':
                    datecount.append(line)
            else:
                if line == '<!-- Date count start -->':
                    dc_useful = True

    if viewed:
        # it should be empty now, otherwise we "viewed movies we didn't have in the list" (surely
        # indicating an error somewhere)
        raise ValueError("Viewed not empty: " + str(viewed))

    # mix with futures
    for _, urlimdb, movie_id in futures:
        movie = imdb.get_movie(movie_id)
        movies.append((movie['title'], urlimdb, NEW_DATE))
    movies.sort()

    # assert there's no repetitions
    counted = collections.Counter(x[1] for x in movies)
    repeated = [x for x, c in counted.items() if c != 1]
    if repeated:
        raise ValueError("Have repeated movies: %s" % (repeated,))

    # generate new peliculas.html
    new_dc = {}
    resp_pelis = []
    for title, urlimdb, date in movies:
        resp_pelis.append(T_PELIS_LINE.format(title=title, urlimdb=urlimdb, date=date))
        new_dc[date] = new_dc.get(date, 0) + 1

    # process date count
    resp_pelis.append("")
    totals = [0] * 20
    len_date = len(NEW_DATE)
    new_lines = []
    for prvcount in datecount:
        prvcount = prvcount.replace("&nbsp;", " ").replace("<br/>", "")

        # separate head and values, being some maybe blanks
        date = prvcount.split()[0]
        assert len(date) == len_date
        rest = prvcount[len_date + 1:]
        vals = []
        while True:
            if rest[:4] and rest[:4] == '    ':
                rest = rest[4:]
                vals.append(0)
            else:
                break
        vals += rest.split()
        new_count = str(new_dc[date]) if date in new_dc else ''
        new_lines.append("{} {:>3s}".format(prvcount, new_count))

        # sum to totals
        for i, v in enumerate(vals):
            totals[i] = totals[i] + int(v)
        totals[i + 1] = totals[i + 1] + new_dc.get(date, 0)
    totals = [x for x in totals if x != 0]

    # finally, the new count, and total
    spaces = "    " * (len(totals) - 1)
    new_lines.append("{} {} {:3d}".format(NEW_DATE, spaces, len(futures)))
    totals[-1] = totals[-1] + len(futures)
    total_line = "Total:     " + "".join("{:4d}".format(x) for x in totals)
    new_lines.append(total_line)

    # filter to not show that much
    while len(new_lines[-1].split()) > 11:  # "total" + the 10 we want to show
        for i, line in enumerate(new_lines):
            new_lines[i] = line[:len_date + 1] + line[len_date + 5:]

    # print them all
    resp_pending = [T_DATECOUNT_HEAD]
    for line in new_lines:
        resp_pending.append("    " + line.strip().replace(" ", "{space}") + "{enter}")
    return resp_pelis, resp_pending


def main(inputfname, outfname):
    """Procesa la fuente y deja los resultados."""
    with open(inputfname, 'rt', encoding='utf8') as fh:
        # first block
        reviews = []
        while True:
            comment = next(fh).strip()
            if not comment:
                # blank line, block separator
                break
            urlimdb = next(fh).strip()
            urlimdb, movie_id = _fix_urlimdb(urlimdb)
            reviews.append((comment, urlimdb, movie_id))

        # second block
        futures = []
        while True:
            try:
                title = next(fh).strip()
            except StopIteration:
                break
            if not title:
                continue
            urlimdb = next(fh).strip()
            urlimdb, movie_id = _fix_urlimdb(urlimdb)
            futures.append((title, urlimdb, movie_id))

    lines, viewed = process_reviews(reviews)
    lines.append("")
    lines.extend(process_futures(futures))
    lines.append("")

    pelis_lines, raw_pending = proc_pelshtml(futures, viewed)

    lines.extend(line.format(enter='', space=' ') for line in raw_pending)
    lines.append("")
    lines.extend(pelis_lines)
    lines.extend(line.format(enter='<br/>', space='&nbsp;') for line in raw_pending)

    with open(outfname, 'wt', encoding='utf8') as fh:
        fh.write("\n".join(lines))


if __name__ == '__main__':
    if len(sys.argv) != 3:
        print("Usar: gen_pelis.py fuente.txt salida.rst")
        exit()

    main(sys.argv[1], sys.argv[2])

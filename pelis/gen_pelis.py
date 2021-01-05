#!/usr/bin/fades

import collections
import datetime
import locale
import operator
import os
import pickle
import re
import sys

import infoauth  # fades
import requests  # fades
# need dep from GH as we use 'original title' yet not released
from imdb import IMDb  # fades git+https://github.com/alberanid/imdbpy.git


T_REVIEWS_HEAD = """\
FIXME: headers!!!

FIXME: Texto inicial acá

Otra linea, etc
"""
T_REVIEWS_BODY = '- `{title} <{url_moviedb}>`_: {vote:}. {explanation:}'

RE_REVIEW_LINE = r'(.*?): ([+-?][\d?]). (.*)'

T_FUTURES_HEAD = """\
FIXME: Mas peliculas anotadas para ver:

Otra linea, etc
"""
T_FUTURES_BODY = '- `{title} <{url_moviedb}>`_: {description}'

RE_PELIS_LINE = '<a href="(.*?)">(.*?)</a> <font size="-2"><i>(.*?)</i></font><br>'
T_PELIS_LINE = '<a href="{url_moviedb:}">{title:}</a> <font size="-2"><i>{date:}</i></font><br>'

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


class TheMovieDB:
    """Simple helper to hit the service."""

    base_url = 'https://api.themoviedb.org/3'

    def __init__(self):
        creds = infoauth.load(os.path.expanduser('~/.tmdb_auth'))
        self.api_key = creds['key']

    def hit(self, endpoint, **params):
        """Hit the DB service."""
        params['api_key'] = self.api_key
        params['language'] = 'es-ES'
        url = self.base_url + endpoint
        response = requests.get(url, params=params)
        data = response.json()
        return data


tmdb = TheMovieDB()


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


def fix_moviedb(url):
    """Fix IMDB or TMdb url."""
    assert url

    if 'imdb' in url:
        # DEPRECATED!!
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

    # get id from the title
    # e.g.: https://www.themoviedb.org/movie/482936-la-quietud
    path = url.split('/')[-1]
    movie_id = int(path.split('-')[0])
    return url, movie_id


def get_movie_info(movie_id):
    """Layer to merge both backends for generic movie info."""
    info = {}
    if isinstance(movie_id, str):
        # DEPRECATED!
        movie = imdb.get_movie(movie_id)
        info['title'] = movie['title']
        info['directors'] = ", ".join(d['name'] for d in movie.get('director', []))
        info['actors'] = ", ".join(a['name'] for a in movie['actors'][:3])
        info['genres'] = ", ".join(movie['genres'])
        info['plot'] = movie.get('plot', ["-"])[0]
        info['year'] = movie['year']
    else:
        movie = tmdb.hit('/movie/{}'.format(movie_id), append_to_response='credits')
        info['title'] = movie['original_title']
        info['genres'] = ", ".join(x['name'] for x in movie['genres'])
        info['plot'] = movie['overview']
        info['year'] = movie['release_date'][:4]

        cast = movie['credits']['cast']
        info['actors'] = ", ".join(x['name'] for x in cast[:5])

        directors = [x for x in movie['credits']['crew'] if x['department'] == 'Directing']
        info['directors'] = ", ".join(x['name'] for x in directors[:2])

    return info


def process_reviews(reviews):
    data = []
    viewed = set()
    for revline, url_moviedb, movie_id in reviews:
        viewed.add(url_moviedb)
        m = re.match(RE_REVIEW_LINE, revline)
        if not m:
            print("ERROR: review line not recognized:", repr(revline))
            exit()
        annot_title, vote, explanation = m.groups()
        movie = get_movie_info(movie_id)
        real_title = movie['title']
        print("Processing review ({}): {}".format(annot_title, real_title))
        datum = dict(title=real_title, vote=vote, explanation=explanation, url_moviedb=url_moviedb)
        data.append(datum)
    data.sort(key=operator.itemgetter('title'))

    resp = [T_REVIEWS_HEAD]
    for datum in data:
        resp.append(T_REVIEWS_BODY.format(**datum))
    return resp, viewed


def process_futures(futures):
    resp = [T_FUTURES_HEAD]
    for annot_title, url_moviedb, movie_id in sorted(futures):
        movie = get_movie_info(movie_id)
        description = "({year}; {genres}) {plot} [D: {directors}; A: {actors}]".format(**movie)

        real_title = movie['title']
        print("Processing futures ({}): {}".format(annot_title, real_title))
        resp.append(T_FUTURES_BODY.format(
            title=real_title, url_moviedb=url_moviedb, description=description))
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
                url_moviedb, title, date = m.groups()
                url_moviedb = url_moviedb.replace('http://', 'https://')
                if url_moviedb in viewed:
                    # clean it in viewed
                    viewed.remove(url_moviedb)
                else:
                    # not viewed, append to the list of movies to remain
                    movies.append((title.strip(), url_moviedb, date))

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
    for _, url_moviedb, movie_id in futures:
        movie = get_movie_info(movie_id)
        movies.append((movie['title'], url_moviedb, NEW_DATE))
    movies.sort()

    # assert there's no repetitions
    counted = collections.Counter(x[1] for x in movies)
    repeated = [x for x, c in counted.items() if c != 1]
    if repeated:
        raise ValueError("Have repeated movies: %s" % (repeated,))

    # generate new peliculas.html
    new_dc = {}
    resp_pelis = []
    for title, url_moviedb, date in movies:
        resp_pelis.append(T_PELIS_LINE.format(title=title, url_moviedb=url_moviedb, date=date))
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
            url_moviedb = next(fh).strip()
            url_moviedb, movie_id = fix_moviedb(url_moviedb)
            reviews.append((comment, url_moviedb, movie_id))

        # second block
        futures = []
        while True:
            try:
                title = next(fh).strip()
            except StopIteration:
                break
            if not title:
                continue
            url_moviedb = next(fh).strip()
            url_moviedb, movie_id = fix_moviedb(url_moviedb)
            futures.append((title, url_moviedb, movie_id))

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

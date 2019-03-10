#!/usr/bin/python3.7

import glob
import re
import socket
import sys
from urllib import request

headers = {
    'User-Agent': "Mozilla/5.0",
}

socket.setdefaulttimeout(5)

# ToDo:
# - cleanup first parts between `` pairs, so we get rid of false ``<bleh>`` thingies
# - ignore 403s from Flickr (don't want all photos just public!!)

FLAG_MISSING = "la url no existe más"


def search_url(fpath):
    with open(fpath, "rt", encoding="utf8") as fh:
        content = fh.read()
    targets = re.findall(":target:(.*)\n", content)
    links = re.findall("`(.*?) \<(.*?)\>`", content, re.S)

    results = []

    for target in targets:
        results.append(target.strip())
    for description, link in links:
        if description != FLAG_MISSING:
            results.append(link.strip())

    completed = []
    for url in set(results):
        if url.startswith("/"):
            url = "https://blog.taniquetil.com.ar" + url
        completed.append(url)

    return completed


def check(url):
    req = request.Request(url, headers=headers)
    try:
        request.urlopen(req).read(2048)
    except Exception as err:
        return repr(err)


def main(posts):
    # if not specific posts indicated, grab them all
    if not posts:
        posts = glob.glob("posts/*.rst")

    all_urls = {}
    for post in posts:
        urls = search_url(post)
        for url in urls:
            all_urls.setdefault(url, []).append(post)

    print("Len URLs:", len(all_urls))
    for i, (url, posts) in enumerate(all_urls.items()):
        try:
            error = check(url)
        except Exception as err:
            print(f"Crash! {err!r}: {url!r}: {posts}")
            continue

        if error:
            print(f"Error {error}: {url!r}: {posts}")
        else:
            print(f"Ok: {url!r} ({i})")


main(sys.argv[1:])

#!/usr/bin/env fades

import glob
import os
import re
import socket
import sys
from urllib import request, parse

import infoauth  # fades
import dropbox  # fades
from bs4 import BeautifulSoup  # fades

headers = {
    'User-Agent': (
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36"),
}

socket.setdefaulttimeout(5)

dropbox_secrets = infoauth.load("/home/facundo/.config/dropbox-secrets")


def search_url(fpath):
    with open(fpath, "rt", encoding="utf8") as fh:
        content = fh.read()
    targets = re.findall(":target:(.*)\n", content)
    links = re.findall(r"`(.*?) \<(.*?)\>`", content, re.S)

    results = set()

    for target in targets:
        results.add(target.strip())
    for description, link in links:
        results.add(link.strip())

    return results


def get_title(url):
    req = request.Request(url, headers=headers)
    try:
        raw = request.urlopen(req).read()
    except Exception as err:
        return repr(err)

    soup = BeautifulSoup(raw, features="html.parser")
    meta_title = soup.find('meta', property="og:title")
    assert meta_title is not None, "title not found"
    return meta_title['content']


def replace(post, old, new):
    with open(post, "rt", encoding='utf8') as fh:
        content = fh.read()
    replaced = content.replace(old, new)
    with open(post, "wt", encoding='utf8') as fh:
        fh.write(replaced)


def main(posts):
    # if not specific posts indicated, grab them all
    if not posts:
        posts = glob.glob("posts/*.rst")

    all_urls = {}
    for post in posts:
        urls = search_url(post)
        for url in urls:
            if 'flickr.com' in url.lower():  # simple filter to discard lot of not flickr stuff
                all_urls.setdefault(url, []).append(post)

    print("All found URLs:", len(all_urls))
    filtered_urls = []
    for url, posts in all_urls.items():
        # really discard anything not flickr from us
        pu = parse.urlparse(url)
        assert 'flickr.com' in pu.netloc, url  # re-validation of above, but more specific
        if '/54757453@N00/' not in pu.path:
            assert '54757453' not in url, url  # just in case
            continue

        filtered_urls.append((url, posts))
    print("Filtered URLs:", len(filtered_urls))

    dbx = dropbox.Dropbox(dropbox_secrets['access_token'])

    # # phase 1: full albums where the name matches our media folder, just point to that
    # for url, posts in filtered_urls:
    #     print(url)
    #     # process
    #     try:
    #         title = get_title(url)
    #     except Exception as err:
    #         print("Crash! {!r}: {!r}: {}".format(err, url, posts))
    #         continue

    #     title = title.strip('/')
    #     our_dirpath = os.path.join("/home/facundo/media/fotos", title)
    #     if not os.path.exists(our_dirpath):
    #         print("    WARNING: not a folder", repr(title))
    #         continue
    #     print("   ", repr(title))

    #     remote_dropbox_path = os.path.join("/.externals/fotos", title)
    #     try:
    #         pathlink = dbx.sharing_create_shared_link(remote_dropbox_path)
    #     except Exception as err:
    #         print("    ERROR: bad sharing:", repr(err))
    #         continue

    #     for post in posts:
    #         print("    processing post", repr(post))
    #         replace(post, url, pathlink.url)

    # phase 2: just the simple photo
    path_per_photo = {}
    basedir = "/home/facundo/media/fotos/"
    for dirpath, dirnames, filenames in os.walk(basedir):
        for fname in filenames:
            path_per_photo[fname] = dirpath[len(basedir):]
    print("Found {} photos paths".format(len(path_per_photo)))

    for url, posts in filtered_urls:
        print(url)
        # process
        try:
            title = get_title(url)
        except Exception as err:
            print("Crash! {!r}: {!r}: {}".format(err, url, posts))
            continue

        title = title.strip('/')
        try:
            our_dirpath = path_per_photo[title]
        except KeyError:
            print("    WARNING: not a folder", repr(title))
            continue
        print("   ", repr(title), repr(our_dirpath))

        remote_dropbox_path = os.path.join("/.externals/fotos", our_dirpath, title)
        try:
            pathlink = dbx.sharing_create_shared_link(remote_dropbox_path)
        except Exception as err:
            print("    ERROR: bad sharing:", repr(err))
            continue

        for post in posts:
            print("    processing post", repr(post))
            replace(post, url, pathlink.url)


main(sys.argv[1:])

#!/usr/bin/env fades

"""
To convert pickled comments from old version blog
to a WXR format specify by Disqus service to import
them into new version blog.
"""


import pickle
import html
from slugify import slugify   # fades python-slugify
from html.parser import HTMLParser

class Blog(object):
    comments = 0
    posts = {}
    authors = {}

    def print_authors(self):
        for id, author in self.authors.items():
            print(id, author.email, author.name, author.uri, sep="; ")

    class Author(object):
        name = None
        email = None
        uri = None

    class Entry(object):
        entry_id = None
        url = None
        permalink = None
        title = None
        title_type = None
        content = None
        content_type = None
        published = None
        updated = None
        author = None
        related = None

    class Post(Entry):
        draft = False

        def __init__(self):
            self.labels = []
            self.comments = []

    class Comment(Entry):
        def __init__(self):
            Blog.comments += 1


class Parser(object):
    def __init__(self, file):
        with open(file, "rb") as handle:
            self.data = pickle.load(handle)
        self.blog = Blog()

    def parse(self):
        for comment in self.data:
            if not "article_id" in comment:
                print("no article id", comment)
                continue
            author = self.parse_author(comment)
            post = self.parse_post(comment)
            comm = Blog.Comment()
            comm.author = author
            comm.content = comment["text"]
            comm.published = comment["date"]
            post.comments.append(comm)
        return self.blog

    def parse_author(self, comment):
        email = comment["email"]
        if comment["email"]:
            author_id = comment["email"]
        elif comment["name"]:
            author_id = comment["name"]
            email = "%s@adhoc-name.com" % slugify(author_id)
        elif comment["url"]:
            author_id = comment["url"]
            if not author_id in self.blog.authors:
                email = "author%04d@adhoc-url.com" % (len(self.blog.authors) + 1)
        else:
            author_id = "Anonymus"
            email = "anonymus@adhoc-name.com"
        if author_id in self.blog.authors:
            author = self.blog.authors[author_id]
        else:
            author = Blog.Author()
            author.email = email
            self.blog.authors[author_id] = author
        author.name = comment["name"]
        author.uri = comment["url"]
        return author

    def parse_post(self, comment):
        if comment["article_id"] in self.blog.posts:
            post = self.blog.posts[comment["article_id"]]
        else:
            post = Blog.Post()
            post.entry_id = comment["article_id"]
            post.content = ''
            post.title = ''
            self.blog.posts[post.entry_id] = post
            post.url = "http://beta.taniquetil.com.ar/posts/%04d/" % post.entry_id
        return post

class WXRWriter(object):
    comment_status = 'open'

    def __init__(self, blog):
        self.blog = blog

    def write(self):
        self.post_id = 0
        self.comment_id = 0

        doc = self.get_header() + self.get_entries() + self.get_footer()
        doc = [line.strip() for line in doc]
        doc = '\n'.join(doc)
        return doc

    def get_header(self):
        res = []
        res.append('<?xml version="1.0" encoding="UTF-8" ?>')
        res.append('<rss version="2.0"')
        res.append('     xmlns:content="http://purl.org/rss/1.0/modules/content/"')
        res.append('     xmlns:dsq="http://www.disqus.com/"')
        res.append('     xmlns:dc="http://purl.org/dc/elements/1.1/"')
        res.append('     xmlns:wp="http://wordpress.org/export/1.0/">')

        res.append('<channel>')

        return res

    def get_footer(self):
        res = []
        res.append('</channel>')
        res.append('</rss>')
        return res

    def get_entries(self):
        res = []

        for id, post in self.blog.posts.items():
            res += self.get_post(post)

        return res

    def get_date(self, ts):
        return ts.strftime("%a, %d %b %Y %H:%M:%S +0000")

    def get_date_wp(self, ts):
        return ts.strftime("%Y-%m-%d %H:%M:%S")

    def escape(self, s):
        return html.escape(s).encode('ascii', 'xmlcharrefreplace')

    def unescape(self, s):
        parser = HTMLParser()
        return parser.unescape(s)

    def get_comment(self, comment):
        status = 1

        res = []

        self.comment_id += 1

        res.append('  <wp:comment>')
        res.append('    <wp:comment_id>%s</wp:comment_id>' % self.comment_id)

        if not comment.author.name:
            comment.author.name = 'Anonymous'
        res.append('    <wp:comment_author><![CDATA[%s]]></wp:comment_author>' % comment.author.name)
        if comment.author.uri:
            res.append('    <wp:comment_author_url><![CDATA[%s]]></wp:comment_author_url>' % comment.author.uri)
        res.append('    <wp:comment_author_email>%s</wp:comment_author_email>' % comment.author.email)
        res.append('    <wp:comment_author_IP>%s</wp:comment_author_IP>' % '')
        res.append('    <wp:comment_date_gmt>%s</wp:comment_date_gmt>' % self.get_date_wp(comment.published))
        res.append('    <wp:comment_content><![CDATA[%s]]></wp:comment_content>' % self.unescape(comment.content))
        res.append('    <wp:comment_approved>%s</wp:comment_approved>' % status)
        if comment.related:
            if comment.related in self.post_comment_ids:
                res.append('    <wp:comment_parent>%s</wp:comment_parent>' % self.post_comment_ids[comment.related])
            else:
                d('could not find related comment %s for comment entry %s (comment_id %s)' % (comment.related, comment.entry_id, self.comment_id))

        res.append('  </wp:comment>')

        return res

    def get_post(self, post):
        res = []

        slug = ''
        if post.url is not None:
            slug = post.url.split('/')[-1]
            slug = slug[:-5]

        self.post_id += 1

        res.append('<item>')
        #res.append('  <title><![CDATA[%s]]></title>' % self.escape(post.title))
        res.append('  <link>%s</link>' % post.url)
        #res.append('  <content:encoded><![CDATA[%s]]></content:encoded>' % self.escape(post.content))
        res.append('  <dsq:thread_identifier>posts/%04d</dsq:thread_identifier>' % post.entry_id)
        res.append('  <wp:comment_status>%s</wp:comment_status>' % self.comment_status)

        self.post_comment_ids = {}

        for comment in post.comments:
            res += self.get_comment(comment)

        res.append('</item>')
        return res

p = Parser("comments/comments-2.pkl")
blog = p.parse()
print("Converted: %d authors, %d posts y %d comments" % (len(blog.authors), len(blog.posts), blog.comments))

writer = WXRWriter(blog)
xml = writer.write()

with open("comments/comments.wxr", "w") as out:
    out.write(xml)

Renders a static list of links into a html to include in the page.

It uses a configuration option in conf.py file, as follows:

```python
STATIC_LINKS = {
    "input_file": "links.yml",
    "output_file": "static_links.html",
    "template": "<div>{}<div>",
    "template_category": "<h2>{title}</h2><ul>{content}</ul>",
    "template_item": '<li><a href="{href}" title="{title}">{data}</a></li>'
}
```

Input file is a plain txt with yaml format, having a list of categories, each one with a name and a list of items.
In each item, you have three keys: href, title and data, like follows

```yaml
- - Categoria-A
  - - data: Link data showed 
      href: link url
      title: link title
    - data: Link data showed
      href: link url
      title: link title
- - Categoria-B
  - - data: Link data showed
      href: link url
      title: link title
    - data: Link data showed
      href: link url
      title: link title
```

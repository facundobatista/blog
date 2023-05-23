import site
site.addsitedir("/home/facundo/blog/issovenv")

from isso import make_app
from isso.core import Config

application = make_app(Config.load("/home/facundo/blog/blog/isso.cfg"))

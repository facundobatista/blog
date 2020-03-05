#!/usr/bin/python
# -*- coding=UTF-8 -*-

import sys, urllib, urllib2, time

class avance:
    def __init__(self, nomarch, largo):
        self.nomarch = nomarch
        self.total = 0
        self.antval = 0
        self.largo = largo
        if self.largo is None:
            print "Bajando '%s'" % self.nomarch
        else:
            print "Bajando '%s' [%s KB]..." % (self.nomarch, largo/1024)

    def step(self, cant):
        self.total += cant
        pt = self.total / 1024
        if pt == self.antval:
            return
        self.antval = pt
        sys.stdout.write("\r%7d KB" % pt)
        sys.stdout.flush()

    def done(self):
        if self.largo is None or self.largo == self.total:
            print "\rTerminado OK"
        else:
            print "Terminado con Error! Largo: %s   Total: %s" % (self.largo, self.total)
        return
        
        
def bajar_youtube(url):
    print "Averiguando la URL real..."

    # bajamos el texto
    u = urllib.urlopen(url)
    t = u.read()
    
    # buscamos lo interesante
    t = t[t.index("watch_fullscreen?video_id=")+26:]
    t = t[:t.index('"')]
    t = t[:t.rindex('&')]
    url = "http://youtube.com/get_video?video_id=" + t
    print url
    
    # abrimos el archivo remoto
    u = urllib.urlopen(url)
    if u.headers["content-type"] != 'video/flv':
        print "El tipo de contenido no es el correcto, sino:", u.headers["content-type"]
        print "Trajimos la info desde:", url
        sys.exit(1)
    largo = int(u.headers["content-length"])
    print u.headers.items()
    if len(sys.argv) > 2:
        archout = sys.argv[2]
    else:
        archout = "video_youtube_%s.flv" % int(time.time())
    print "--largo", largo
    bajaArch(u, archout, largo)
    return


def bajar_google(url):
    print "Averiguando la URL real..."

    # bajamos el texto
    u = urllib.urlopen(url)
    t = u.read()
    
    # buscamos lo interesante
    t = t[t.index("insertFlashHtmlOnLoad"):]
    t = t[t.index("http"):]
    t = t.split()[0]
    url = urllib2.unquote(t)
    #print url
    
    # abrimos el archivo remoto
    u = urllib.urlopen(url)
    if u.headers["content-type"] != 'video/x-flv':
        print "El tipo de contenido no es el correcto, sino: %s... reintentando." % u.headers["content-type"]
        # intentamos a ver si la url real está más adelante (por ejemplo, video.google.de es así...)
        t = url[url.index("http", 1):]
        t = t.split()[0]
        url = urllib2.unquote(t)
        #print url

        # abrimos el archivo remoto... otra vez!
        u = urllib.urlopen(url)
        if u.headers["content-type"] != 'video/x-flv':
            print "El tipo de contenido no es el correcto, sino:", u.headers["content-type"]
            sys.exit(1)

    a = u.headers["content-disposition"]
    archout = a[a.index("filename=")+9:]
    bajaArch(u, archout)
    return



def buscaUrl(todo, ext, desdeatras=False):
    if desdeatras:
        t = todo[:todo.rindex(ext)+len(ext)]
    else:
        t = todo[:todo.index(ext)+len(ext)]
    return t[t.rindex("http"):]
    

def bajar_apple(url):
    print "Averiguando la URL real..."

    # bajamos el texto
    u = urllib.urlopen(url)
    t = u.read()

    # buscamos lo interesante
    if "embedvideo.js" in t:
        urljs = buscaUrl(t, "embedvideo.js")
        js = urllib2.urlopen(urljs).read()
        t = buscaUrl(js, ".mov", desdeatras=True)
    elif "clicktoplay.mov" in t:
        t = t[t.index("clicktoplay.mov"):]
        t = t[t.index("href"):]
        t = buscaUrl(t, ".mov")
    else:
        t = buscaUrl(t, ".mov")

    p = t.split("/")
    if t.endswith("-h.ref.mov"):
#        url = t[:-10] + "_h320.mov"
        url = t[:-10] + "_h480.mov"
        archout = p[-1][:-10] + ".mov"
    elif t.endswith("_h.640.mov"):
        url = t[:-10] + "_h640w.mov"
        archout = p[-1][:-10] + ".mov"
    elif t.endswith("_h.i320.mov"):
        url = t[:-11] + "_h320.mov"
        archout = p[-1][:-11] + ".mov"
    elif t.endswith("_h.i640.mov"):
        url = t[:-11] + "_h640w.mov"
        archout = p[-1][:-11] + ".mov"
    elif t.endswith("_h.480.mov"):
        url = t[:-10] + "_h480.mov"
        archout = p[-1][:-10] + ".mov"
    elif t.endswith("_h480.mov"):
        url = t
        archout = p[-1][:-10] + ".mov"
    else:
        print "El formato de la URL es incorrecto:", t
        sys.exit(1)
#    print url
    
    # abrimos el archivo remoto
    u = urllib.urlopen(url)
    if u.headers["content-type"] != 'video/quicktime':
        print "El tipo de contenido no es el correcto, sino:", u.headers["content-type"]
        print "Trajimos la info desde:", url
        sys.exit(1)
    largo = int(u.headers["content-length"])
    bajaArch(u, archout)
    return
    

def bajaArch(u, archout, largo=None):
    # y lo bajamos
    av = avance(archout, largo)
    aout = open(archout, "wb")
    while True:
        r = u.read(4096)
        if r == "":
            break
        aout.write(r)
        av.step(len(r))
    av.done()
    return


# diccionario que decide qué función usar...
FUNC = {"www.youtube.com": bajar_youtube,
        "youtube.com":     bajar_youtube,
        "www.apple.com":   bajar_apple,
        "movies.apple.com":   bajar_apple,
        "video.google.com":   bajar_google,
        "video.google.de":   bajar_google,
       }

if __name__ == "__main__":
    if len(sys.argv) < 2 or len(sys.argv) > 3:
        print "Usar: bajavideo.py <url> [archivo_destino]"
        print "      archivo_destino solo es usado con YouTube"
        sys.exit(1)

    url = sys.argv[1]
    partes = urllib2.urlparse.urlsplit(url)
    server = partes[1]
    if server not in FUNC:
        print "No se reconoce a quien pertenece la url '%r'" % server
        sys.exit(1)

    func = FUNC[partes[1]]
    func(url)

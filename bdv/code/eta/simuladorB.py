#-*- coding: utf8 -*-

from __future__ import division
import wave, random, itertools
from PIL import Image, ImageDraw


class Dibujo(object):
    def __init__(self, valsx, valsy):
        self.cantx = len(valsx)
        for y in valsy:
            if len(y) != self.cantx:
                raise ValueError("No tenemos la misma cantidad de puntos en uno de los Y que en el X")
        # calculamos el alto
#        print "dib"
        self.ymax = self.ymin = 0
        for curva in valsy:
            mx = max(curva)
            mn = min(x for x in curva if x is not None)
#            print mx, mn
            self.ymax = max(mx, self.ymax)
            self.ymin = min(mn, self.ymin)
        self.valsx = valsx
        self.valsy = valsy
        self.alto = self.ymax - self.ymin
#        print self.ymax, self.ymin, self.alto

    def __str__(self):
        return "Dibujo (%d) %s" % (self.cantx, self.valsy)


class Curvas(object):
    def __init__(self, nomarch):
        self.nomarch = nomarch
        self.dibs = []

        # params de dibujo
        self.separ_y = 20
        self.margen_x = 10
        self.margen_y = 10
        self.colores =  ((127,127,127), (0,0,0), (0,0,255), (0,255,0), (255,0,0))
        self.color_fondo = (250,250,250)
        self.formato = "PNG"

    def append(self, valsx, *valsy):
        d = Dibujo(valsx, valsy)
        self.dibs.append(d)

    def close(self):
        # calculamos el ancho y alto de toda la imagen
        ancho = max(d.cantx for d in self.dibs)
        ancho += self.margen_x * 2
        altura = 0
        for dib in self.dibs:
            altura += dib.alto
        altura += self.separ_y * (len(self.dibs) - 1)
        altura += self.margen_y * 2
        altura = int(altura)
#        print "ancho, alto", ancho, altura

        # creamos la imagen y dibujamos
        im = Image.new("RGB", (ancho, altura), self.color_fondo)
        self._draw = ImageDraw.Draw(im)
        deltah = self.margen_y
        newdibs = []
        for dib in self.dibs:
            ndib = self._retocamos(dib, deltah)
            deltah += dib.alto + self.separ_y
            newdibs.append(ndib)

        for dib in newdibs:
            self._dibujamos(dib)

        # grabamos
        im.save(self.nomarch, self.formato)

    def _dibujamos(self, dib):
        ivals = itertools.izip(dib.valsx, *dib.valsy)
        ini = ivals.next()
        antx = ini[0]
        anty = ini[1:]
        for puntos in ivals:
            x,ys = puntos[0], puntos[1:]
            for i,y in enumerate(ys):
                if anty[i] is not None and y is not None:
                    self._draw.line([(antx, anty[i]), (x, y)], self.colores[i])
            anty = ys
            antx = x

    def _retocamos(self, dib, delta):
        '''Facilitamos la info para dibujar.
        
        Convertimos a "0 es arriba" a los Y del dibujo, y le
        sumamos el deltah.
        '''
        valsy = []
        puntosup = dib.ymax
        for vals in dib.valsy:
            modif = []
#            print vals
            for val in vals:
                if val is None:
                    newy = None
                else:
                    newy = (puntosup-val) + delta
                modif.append(newy)
            valsy.append(modif)
#            print modif
        valsx = []
        for val in dib.valsx:
            valsx.append(self.margen_x + val)
        return Dibujo(valsx, valsy)



def generaFuente():
    '''Genera datos para luego analizar como mostraríamos el pendiente.

    Las reglas son las siguientes:
    
    - Arranca en 0
    - Tres puntos en 0 (como si fuera el tiempo de conexión)
    - Pega un salto a 50 (como que se conectó)
    - Siempre con límites entre [0,99], varía random:
        -  1% se desconecta, espera 10 puntos, chances 10 % reconexión
        -  4% picos de 2 de ancho, de +/- 40 y luego de los 2 vuelve al orig
        - 70% variación al azar [-2,2]
        - 15% variación al azar [-5,5]
        - 10% variación al azar [-10,10]
    '''

    def limite(val):
        if val < 0:
            return 0
        if val > 99:
            return 99
        return val

    # arranque 
    for i in range(3):
        yield 0
    ult = 50
    yield ult

    # avanzamos
    while True:
        chances = random.randint(0,99)
        if chances < 1:
            # no modifica el último
            yield 0
            while random.randint(0,99) >= 10:
                yield 0
        elif 1 <= chances < 5:
            # no modifica el último
            signo = -1 ** random.randint(0,1)
            pico = ult + (signo * 20)
            yield limite(pico)
            yield limite(pico)
        elif 5 <= chances < 75:
            ult = limite(ult + random.randint(-2,2))
            yield ult
        elif 75 <= chances < 90:
            ult = limite(ult + random.randint(-5,5))
            yield ult
        elif 90 <= chances < 100:
            ult = limite(ult + random.randint(-10,10))
            yield ult


class PromDinamC1(object):
    '''Esta es la que hace magia, :)
    
    Se toma el delta último, que es "N - N_-1", y el delta del último grupo,
    que es la diferencia entre el máximo y el mínimo de los últimos 5 puntos.

    Si el delta último es mayor que el delta del grupo, mostramos el delta
    del grupo. Esto aplanaría la curva ante los cambios grandes.
    
    El único punto a ver de corregir, si esto funciona, es el número mágico.
    '''

    def __init__(self):
        # nos dice si debemos agrandar o achicar la cantidad de
        # valores para el promedio
        self.cantgrupo = 20

        # variables resto
        self.valores = []
        self.primVez = True

    def step(self, val):
        # si no tenemos valores, a comerla
        if self.primVez:
            self.valores.append(val)
            self.primVez = False
            return val

        # delta mayor entre algunos de los anteriores y el promedio anterior
        valsgrupo = self.valores[-self.cantgrupo:]
        promant = sum(valsgrupo) / self.cantgrupo
        deltagrupo = max(abs(x-promant) for x in valsgrupo)
#        print "\n", val, valsgrupo, promant, deltagrupo

        # si el delta último es menor que el otro, sacamos el nuevo valor con el último
        deltaultimo = abs(val - valsgrupo[-1])
        if deltaultimo <= deltagrupo:
#            print "Usamos deltaultimo", deltaultimo
            valsgrupo.append(val)
        # como es mayor, recortamos el último valor para el promedio
        else:
            if val > valsgrupo[-1]:
                recort = val+deltagrupo
            else:
                recort = val-deltagrupo
#            print "Usamos recortado", recort
            valsgrupo.append(recort)
        
        # obviamente agregamos el verdadero a la lista
        self.valores.append(val)

        # pero devolvemos el promedio en funcion del recortado o no
        prom = sum(valsgrupo) / len(valsgrupo)
#        print valsgrupo, prom
        return prom


class PromDinamC2(object):
    '''Esta es la que hace magia, :)
    
    Se toma el delta último, que es "N - N_-1", y el delta del último grupo,
    que es la diferencia entre el máximo y el mínimo de los últimos 5 puntos.

    Si el delta último es mayor que el delta del grupo, mostramos el delta
    del grupo. Esto aplanaría la curva ante los cambios grandes.

    Este es una variación del anterior, siendo el tamaño del grupo variable 
    en función de los puntos recorridos, y si pasamos la mitad o no, siempre
    con un menor de 5.
    '''

    def __init__(self):
        self.valores = []
        self.primVez = True
        self._mitad = None

    def mitad(self):
        if self._mitad is None:
            self._mitad = len(self.valores) * 2

    def step(self, val):
        # si no tenemos valores, a comerla
        if self.primVez:
            self.valores.append(val)
            self.primVez = False
            return val

        # calculamos la cantidad del grupo
        if self._mitad is None:
            # primera mitad
            cantgrupo = len(self.valores)
        else:
            # segunda mitad
            cantgrupo = (self._mitad - len(self.valores))
        cantgrupo = int(cantgrupo)
        if cantgrupo < 5:
            cantgrupo = 5

        # delta mayor entre algunos de los anteriores y el promedio anterior
        valsgrupo = self.valores[-cantgrupo:]
        promant = sum(valsgrupo) / cantgrupo
        deltagrupo = max(abs(x-promant) for x in valsgrupo)
#        print "\n", val, valsgrupo, promant, deltagrupo

        # si el delta último es menor que el otro, sacamos el nuevo valor con el último
        deltaultimo = abs(val - valsgrupo[-1])
        if deltaultimo <= deltagrupo:
#            print "Usamos deltaultimo", deltaultimo
            valsgrupo.append(val)
        # como es mayor, recortamos el último valor para el promedio
        else:
            if val > valsgrupo[-1]:
                recort = val+deltagrupo
            else:
                recort = val-deltagrupo
#            print "Usamos recortado", recort
            valsgrupo.append(recort)
        
        # obviamente agregamos el verdadero a la lista
        self.valores.append(val)

        # pero devolvemos el promedio en funcion del recortado o no
        prom = sum(valsgrupo) / len(valsgrupo)
#        print valsgrupo, prom
        return prom
        

def calcula(etf, desc, falta, limsup=300):
    vprom = etf.step(desc)
    try:
        resto = falta / vprom
    except ZeroDivisionError:
        resto = None
    if resto > limsup:
        resto = limsup
    return (vprom, resto)

if __name__ == "__main__":
    # descarga total
    desctot = descfalta = 20000

    # lo recorremos, procesando
    etf1 = PromDinamC1()
    etf2 = PromDinamC2()
    veloc = []
    proms1 = []
    proms2 = []
    indics1 = []
    indics2 = []
    for desc in generaFuente():
        descfalta -= desc
        if descfalta <= 0:
            break
        veloc.append(desc)

        (p, i) = calcula(etf1, desc, descfalta)
        proms1.append(p)
        indics1.append(i)

        (p, i) = calcula(etf2, desc, descfalta)
        proms2.append(p)
        indics2.append(i)
        if descfalta < desctot/2:
            etf2.mitad()

    # dibujamos
    valsx = range(len(veloc))
    dib = Curvas("onda.png")
    dib.append(valsx, veloc, proms1, indics1)
    dib.append(valsx, veloc, proms2, indics2)
    dib.close()

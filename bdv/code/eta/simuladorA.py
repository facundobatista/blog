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
        print "ancho, alto", ancho, altura

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
                if anty[i] is not None:
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


class Puntos:
    ''' Facilita la generación de los puntos originales.'''
    def __init__(self, cant, base=[]):
        self.altura_max = 99
        self.cant = cant
        self.puntos = base

    def falta(self):
        return len(self.puntos) < self.cant

    def getPuntos(self):
        return self.puntos[:self.cant]

    def limite(self, val):
        if val < 0:
            return 0
        if val > self.altura_max:
            return self.altura_max
        return val

    def agrega(self, delta):
        h = self.puntos[-1] + delta
        self.puntos.append(self.limite(h))

    def extiende(self, deltas):
        x = self.puntos[-1]
        for delt in deltas:
            h = x + delt 
            self.puntos.append(self.limite(h))
        self.puntos.append(x)


def generaFuente(cant):
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
    # arranque 
    punt = Puntos(cant, [0,0,0,50])

    # avanzamos
    while punt.falta():
        chances = random.randint(0,99)
        if chances < 1:
            alt = punt.puntos[-1]
            encero = 10
            while random.randint(0,99) >= 10:
                encero += 1
            punt.extiende([-alt]*encero)
        elif 1 <= chances < 5:
            signo = -1 ** random.randint(0,1)
            pico = signo * 20
            punt.extiende([pico, pico])
        elif 5 <= chances < 75:
            delta = random.randint(-2,2)
            punt.agrega(delta)
        elif 75 <= chances < 90:
            delta = random.randint(-5,5)
            punt.agrega(delta)
        elif 90 <= chances < 100:
            delta = random.randint(-10,10)
            punt.agrega(delta)

    return punt.getPuntos()


class PromDinamA(object):
    '''Esta es la que hace magia, :)
    
    Usamos un promedio de largo variable: si el indicador se pone nervioso,
    alargamos la cantidad de datos en el promedio, si se estabiliza, la
    achicamos. Si tocamos la cantidad, volvemos a revisar el promedio con la
    nueva cantidad.

    Tener en cuenta de que noo mostramos desde el principio, esperamos
    n cambios en los valores que recibimos, como para evitar el "transient"
    inicial.
    '''

    def __init__(self):
        # nos dice si debemos agrandar o achicar la cantidad de
        # valores para el promedio
        self.lim_nerv = 0.10
        self.lim_trnq = 0.01

        # para eliminar el transient inicial
        self.cant_variaciones = 5
        self.estable = False

        # variables resto
        self.cantprom = 1
        self.valores = []
        self.promant = 0

    def step(self, val):
        # construimos nuestra lista de valores
        self.valores.append(val)

        # calculamos el promedio, y si corresponde agrandamos/reducimos
        # la cantidad de valores para el próximo promedio
        valsprom = self.valores[-self.cantprom:]
        prom = sum(valsprom) / self.cantprom
        if self.promant == 0:
            if prom != 0:
                self.cantprom += 1
                prom = sum(valsprom) / self.cantprom
        elif abs((prom-self.promant)/self.promant) > self.lim_nerv:
            self.cantprom += 1
            prom = sum(valsprom) / self.cantprom
        elif self.promant != 0 and abs((prom-self.promant)/self.promant) < self.lim_trnq and self.cantprom > 1:
            self.cantprom -= 1
            prom = sum(valsprom) / self.cantprom
        self.promant = prom

        # si no tenemos muchas variaciones, nos fuimos
        if not self.estable:
            cant_distintos = len(set(self.valores))
            if cant_distintos > self.cant_variaciones:
                self.estable = True
            else:
                return int(prom)
        return int(prom)
        

class PromDinamB(object):
    '''Esta es la que hace magia, :)
    
    Usamos un promedio de largo variable: si el indicador se pone nervioso,
    alargamos la cantidad de datos en el promedio hasta que lo "aquietamos" lo
    suficiente. Si se estabiliza, vamos bajando de a uno la cantidad de valores
    tomados.
      
    Tener en cuenta de que no mostramos desde el principio, esperamos
    n cambios en los valores que recibimos, como para evitar el "transient"
    inicial.
    '''

    def __init__(self):
        # nos dice si debemos agrandar o achicar la cantidad de
        # valores para el promedio
        self.lim_nerv = 10
        self.lim_trnq = 1

        # para eliminar el transient inicial
        self.cant_variaciones = 5
        self.estable = False

        # variables resto
        self.cantprom = 1
        self.valores = []
        self.promant = 0

    def step(self, val):
        # construimos nuestra lista de valores
        self.valores.append(val)
#        print "\n", val, self.promant, self.cantprom

        # calculamos el promedio tomando más o menos valores segun corresponda
        agrand = 0
        while True:
            valsprom = self.valores[-self.cantprom:]
            prom = sum(valsprom) / self.cantprom

            if self.promant != 0:
                delta = abs(prom-self.promant)/self.promant*100
            else:
                delta = 0
#            print valsprom, delta

            # ¿nos estamos estabilizando?
            if delta < self.lim_trnq:
#                print "estabilizandose"
                if self.cantprom > 1:
                    self.cantprom -= 1
                break

            # si no está muy nervioso, nos fuimos
            if delta < self.lim_nerv:
                break

#            print "muy nervioso"
            if self.cantprom == len(self.valores) or agrand>4:
                # no importa si esta nervioso, ya tenemos todos los valores
                break
            self.cantprom += 1
            agrand += 1

        self.promant = prom

        # si no tenemos muchas variaciones, nos fuimos
        if not self.estable:
            cant_distintos = len(set(self.valores))
            if cant_distintos > self.cant_variaciones:
                self.estable = True
            else:
#                print self.cantprom, prom
                return int(prom)
        
#        print self.cantprom, prom
        return int(prom)
        

class PromDinamC(object):
    '''Esta es la que hace magia, :)
    
    Se toma el delta último, que es "N - N_-1", y el delta del último grupo,
    que es la diferencia entre el máximo y el mínimo de los últimos 5 puntos.

    Si el delta último es mayor que el delta del grupo, mostramos el delta
    del grupo.
      
    Esto apalanaría la curva ante los cambios grandes. El único punto a ver de
    corregir, si esto funciona, es el número mágico.
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
        

if __name__ == "__main__":
    # armamos la fuente de la simulación
    puntos = generaFuente(400)

    # lo recorremos, procesando
    etfA = PromDinamA()
    etfB = PromDinamB()
    etfC = PromDinamC()
    data0 = []
    dataA = []
    dataB = []
    dataC = []
    for p in puntos:
        data0.append(p)
        dataA.append(etfA.step(p))
        dataB.append(etfB.step(p))
        dataC.append(etfC.step(p))

    # dibujamos
    valsx = range(len(data0))
    dib = Curvas("onda.png")
    dib.append(valsx, data0, dataA)
    dib.append(valsx, data0, dataB)
    dib.append(valsx, data0, dataC)
    dib.close()

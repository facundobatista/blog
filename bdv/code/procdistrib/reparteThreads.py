# -*- coding: UTF-8 -*-

import threading, time

debugmode = False

class MiHilo(threading.Thread):
    def __init__(self, funcion, args):
        self.funcion = funcion
        self.args = args
        threading.Thread.__init__(self)
        
    def run(self):
#        print "Ejecutando la funcion %r con args %r" % (id(self.funcion), self.args)
        self.result = self.funcion(*self.args)
#        print "Fin funcion %r, result: %r" % (id(self.funcion), self.result)
        return


class Repartidor:
    """Clase que recibe varios destinos y una función.

    El método "enviar" sirve para ejecutar dicha función en algún servidor
    libre de los que recibió al principio. Si hay uno libre, genera un
    thread para usarlo.
    """

    def __init__(self, destinos, nombreFunc):
        self.destinos = destinos
        self.nombreFunc = nombreFunc
        self._cantdest = len(destinos)
        self._disponibles = [True] * self._cantdest
        self._hilos = [None] * self._cantdest
        self._resultados = []

    def enviar(self, *args):
        """
        Función que llama a la función guardada en un servidor libre.
        Se bloquea hasta que algun servidor queda disponible.
        """
        
        # nos colgamos hasta tener disponible un server
        while sum(self._disponibles) == 0:
            for pos in range(self._cantdest):
                h = self._hilos[pos]
                if not h.isAlive():
                    if debugmode: print "El hilo %r terminó!!!" % h
                    self._resultados.append(h.result)
                    self._disponibles[pos] = True
            time.sleep(.5)

#        print "Estado servers", self._disponibles
        nroDest = self._disponibles.index(True)
        destino = self.destinos[nroDest]
        funcion = getattr(destino, self.nombreFunc)
        self._disponibles[nroDest] = False
        hilo = MiHilo(funcion, args)
        if debugmode: print "Lazando el hilo %r" % hilo
        hilo.start()
        if debugmode: print "El hilo %r fue lanzado" % hilo
        self._hilos[nroDest] = hilo
        return

    def terminar(self):
        """
        Vuelve solamente cuando terminaron todos los hilos que
        había disparado.
        """

        while sum(self._disponibles) < self._cantdest:
            revisar = [pos for (pos, val) in enumerate(self._disponibles) if not val]
#            print "Terminando:", self._disponibles, revisar
            for i in revisar:
                h = self._hilos[i]
                if not h.isAlive():
                    if debugmode: print "El hilo %r terminó!!!" % h
                    self._resultados.append(h.result)
                    self._disponibles[i] = True
            time.sleep(.5)
#        print "Terminaron toooooooooodos"
        return self._resultados

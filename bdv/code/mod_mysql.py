#-*- coding: utf-8 -*-

import MySQLdb, sys, threading

_reconectables = (
    2002, # (CR_CONNECTION_ERROR) Can't connect to local MySQL server through socket '%s' (%d)
    2003, # (CR_CONN_HOST_ERROR) Can't connect to MySQL server on '%s' (%d)
    2006, # (...) MySQL server has gone away
    2012, # (CR_SERVER_HANDSHAKE_ERR) Error in server handshake
    2013, # (CR_SERVER_LOST) Lost connection to MySQL server during query
)

class DataBase:
    '''Módulo que facilita la interacción con el MySQL.

    Tiene dos métodos:
     - accion: impacta el comando que se le pase y devuelve el resultado si 
       el comando era un SELECT, en otro caso solamente None
     - muestra: muestra de forma prolija los últimos resultados obtenidos.
    
    Deja en algunos registros internos, algo de info útil:
     - affected: Cantidad de registros modificados por el último comando. Ojo
       que esta variable no es threading safe.

    Podemos modificar el comportamiento de la clase mediante los siguientes
    atributos (el default entre corchetes):
     - debugmode: En True, siempre muestra el comando que estamos impactando
       [False].
     - silencio: En True no aclara que errorcode obtuvimos en caso de error
       (aunque siempre se ve el traceback) [False].
    '''
    def __init__ (self, server, usuario, clave, base):
        # levantamos la conexión
        self._connect_info = (server, usuario, clave, base)
        self.db = MySQLdb.Connect(*self._connect_info)
        self.cursor = self.db.cursor()

        # lo ponemos en autocommit, que es lo que estamos acostumbrados 
        # por la linea de comando del mysql
        self.cursor.execute("set autocommit=1;")
        
        # variables del objeto
        self.affected = None
        self.debugmode = False
        self.silencio = False
        self.lock = threading.Lock()

    def accion (self, comando):
        '''Impacta el comando que se le pase.
        
        Devuelve el resultado si el comando era un SELECT, en otro caso
        solamente None.
        '''
        if self.debugmode:
            print " *", comando

        # impactamos en la base
        self.lock.acquire(True) # blocking...
        try:
            try:
                self.affected = self.cursor.execute(comando)
                result = self.cursor.fetchall()
            except Exception, err:
                if not self.silencio:
                    print "Error al hacer el query: %r" % err
                    print "--->", comando

                # Soporte para reconexión ante un problema
                if err.args[0] in _reconectables:
                    if not self.silencio:
                        print "Es un error RECONECTABLE..."
                    self.db = MySQLdb.Connect(*self._connect_info)
                    self.cursor = self.db.cursor()
                    if not self.silencio:
                        print "Nos reconectamos OK, :)"
                raise
        finally:
            self.lock.release()
        return result

    def muestra (self):
        for elem in self.result:
            print elem

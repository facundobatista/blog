import xmlrpclib, time, sys
import reparteThreads

#reparteThreads.debugmode = True

usage = """
Usar  client_processes.py sever:port [[server:port] ...]

ej: client_processes.py  localhost:9000  10.12.33.112:9000  10.12.33.113:9000
"""

if len(sys.argv) < 2:
	print usage
	sys.exit(-1)
servers = sys.argv[1:]

servers = [xmlrpclib.Server('http://' + x) for x in servers]
repartidor = reparteThreads.Repartidor(servers, "factoriz_sum")

base = 23434252232434

tini = time.time()
for i in range(10):
    repartidor.enviar(str(base+i))
resultados = repartidor.terminar()
print "\n".join(resultados)
print "Tiempo:", time.time() - tini


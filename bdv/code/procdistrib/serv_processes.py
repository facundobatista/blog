import SimpleXMLRPCServer, sys
import factorizar

usage = """
Usar  serv_processes.py nroport

ej: serv_processes.py 9000
"""

if len(sys.argv) != 2:
	print usage
	sys.exit(-1)
port = int(sys.argv[1])

server = SimpleXMLRPCServer.SimpleXMLRPCServer(('localhost', port))
server.register_function(factorizar.factoriz_sum)
server.serve_forever()



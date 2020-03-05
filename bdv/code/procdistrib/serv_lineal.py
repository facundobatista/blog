import SimpleXMLRPCServer
import factorizar

server = SimpleXMLRPCServer.SimpleXMLRPCServer(('localhost', 9000))
server.register_function(factorizar.factoriz_sum)
server.serve_forever()

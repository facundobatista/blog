import SimpleXMLRPCServer

def cuad(n):
    return n**2
    
server = SimpleXMLRPCServer.SimpleXMLRPCServer(('localhost', 9000))
server.register_function(cuad)

server.serve_forever()

import xmlrpclib, time

serv = xmlrpclib.Server('http://localhost:9000')

result = serv.cuad(7)
print result

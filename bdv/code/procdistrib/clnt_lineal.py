import xmlrpclib, time

s = xmlrpclib.Server('http://localhost:9000')

base = 23434252232434

tini = time.time()
for i in range(10):
	res = s.factoriz_sum(str(base + i))
	print res
print "Tiempo:", time.time() - tini

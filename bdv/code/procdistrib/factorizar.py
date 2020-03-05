import math

def myRange(ini, fin):
	cont = ini
	while cont < fin:
		yield cont
		cont += 1

def factoriz_sum(n):

	if isinstance(n ,str) and n.isdigit():
		n = long(n)
	
	if not isinstance(n , (int, long)):
		raise TypeError, "El valor recibido debe ser string o entero!"

	results = []

	final = long(math.sqrt(n))
	while n > 1:
		for i in myRange(2,final):
			if n%i == 0:
				results.append(i)
				n /= i
				break
		else:
			results.append(n)
			break
	return str(sum(results))

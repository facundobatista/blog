
import decimal
import math
import sys
import time

# http://www.astromia.com/astronomia/nuestravia.htm
anioluz = decimal.Decimal("9.46e+15")  # en metros
radio_galaxia = 100000 * anioluz


# constantes para la formula de PI: http://www.cs.uwaterloo.ca/~alopez-o/math-faq/node38.html
k1 = 545140134
k2 = 13591409
k3 = 640320
k4 = 100100025
k5 = 327843840
k6 = 53360
h1 = (k6 * decimal.Decimal(k3).sqrt())


def sumatando(n):
    """Lo de adentro de la sumatoria."""
    h2 = (-1) ** n * math.factorial(6 * n) * (k2 + n * k1)
    h3 = math.factorial(n) ** 3 * math.factorial(3 * n) * (8 * k4 * k5) ** n
    res = decimal.Decimal(h2) / h3
    return res


def sumatoria(limite):
    """La sumatoria en forma de generador."""
    n = 0
    while True:
        s = sumatando(n)
        yield s
        n += 1
        if n == limite:
            return


def calcula(vueltas):
    """Calcula y devuelve pi."""
    tot = 0
    for x in sumatoria(vueltas):
        tot += x
    pi = h1 / tot
    return pi


def main():
    vueltas = 1
    pi_m1 = 0
    pi_m0 = 0
    while True:
        tini = time.time()
        pi = calcula(vueltas)
        demora = time.time() - tini
        print("%2d %.3f" % (vueltas, demora))
        if pi == pi_m0:
            print(pi_m1)
            print(pi_m0)
            perim_m1 = 2 * pi_m1 * radio_galaxia
            perim_m0 = 2 * pi_m0 * radio_galaxia
            print("dif", perim_m0 - perim_m1)
            return
        pi_m1 = pi_m0
        pi_m0 = pi
        vueltas += 1

if __name__ == "__main__":
    if len(sys.argv) == 2:
        c = decimal.getcontext()
        c.prec = int(sys.argv[1])
    main()

.. title: Minecraft y la electrónica
.. date: 2020-01-19 18:01:00
.. tags: Minecraft, electrónica, compuertas, NAND, flip-flop

Una de las cosas que más me cautivó siempre de Minecraft, además de su total libertad para construir y alterar "el mundo", es que esas construcciones no son pasivas, sino que existen actuadores, generadores de energía, etc.

Claro, todo es un poco simplificado y "raro" a nivel de la física como la conocemos, pero eso es en pos de la jugabilidad, y aunque a veces puede ser un incordio (hay que aprenderse esos detalles), no es realmente un problema.

Por ejemplo, un elemento típico que en la realidad es sólo un interruptor, en Minecraft es un generador de energía: el botón. Y sólo con poner cerca ese botón con un actuador (por ejemplo, una puerta), tenemos el circuito armado.

FIXME: foto puerta con botón
.. image:: /images/budapest-frio1.jpeg   # FIXME
    :alt: El auto tapado de hielo, ese frío hacía   # FIXME
    :target: url!!   # FIXME

Ahí no hay nada más que lo que vemos, la puerta y el botón sobre el bloque de FIXME. Si en el juego vamos y apretamos ese botón, la puerta se abre.


El polvo rojo
-------------

Pero Minecraft introduce un elemento más que lo cambia todo: la "piedra roja", o "redstone".

FIXME: explicar "formalmente" que es redstone, y donde se consigue

FIXME Con el polvo de piedra roja podemos armar conexiones, cables?

FIXME: foto piedra, polvo, elementitos básicos?
.. image:: /images/budapest-frio1.jpeg   # FIXME
    :alt: El auto tapado de hielo, ese frío hacía   # FIXME
    :target: url!!   # FIXME

Durante mucho tiempo estuve con la idea de hacer algo de electrónica dentro del juego, el otro día me decidí, y armé una compuerta NAND. 

Una compuerta NAND es una de los elementos clásicos de la electronica, y no es más que una AND negada. Aunque al aprender electrónica digital siempre se empieza por ANDs y ORs, que son más fáciles, la electrónica realmente gira alrededor de las NANDs porque su implementación (su fabricación) es la más sencilla de todas. FIXME: link a esto.

Al tratar de armar esto dentro del juego me di cuenta que se aplicaba esas reglas raras que a veces tiene el juego o redstone en particular, y tuve que buscar por ahí cómo se podía hacer la compuerta. No es tan difícil, pero implica usar "antorchas de piedra roja" (redstone torches FIXME(revisar nombre).

FIXME: explicar qué es formalmente

Armé entonces la compuerta NAND, usando dos palancas (que generan un 0 o un 1 en función de la posición) y una puerta para mostrar el estado de salida:

FIXME: cuatro verticales con la tabla de verdad de la compuerta, con una vista un poco "diagonal de arriba"
.. image:: /images/budapest-frio1.jpeg   # FIXME
    :alt: El auto tapado de hielo, ese frío hacía   # FIXME
    :target: url!!   # FIXME


Electrónica más compleja
------------------------

Una vez que tuve la NAND, el resto es cuestión de escalar en complejidad. Entonces decidí hacer un flip-flop, para mediante dos botones, mantener un estado de salida.

Un flip-flop es FIXME(explicar formalmente). 

Hay de distintos tipos, pero apunté al más sencillo, el set-reset. FIXME: explicar formalmente

FIXME: diagrama del S-R con las compuertas
.. image:: /images/budapest-frio1.jpeg   # FIXME
    :alt: El auto tapado de hielo, ese frío hacía   # FIXME
    :target: url!!   # FIXME

Entonces, armé ese circuito, y puse una puerta en FIXME (la otra salida no me interesa). Fíjense que la entrada "set" y "reset" están negadas (la barrita horizontal arriba de la S y la R). Estuve viendo cómo hacer un negador simple en Minecraft y no me salió, así que opté por un viejo truco del electrónico, usar una NAND (¿les dije que son la base de todo?) con las entradas unidas.

El circuito anda de perlas. FIXME explicar algo más


FIXME: foto diagonal de arribita
.. image:: /images/budapest-frio1.jpeg   # FIXME
    :alt: El auto tapado de hielo, ese frío hacía   # FIXME
    :target: url!!   # FIXME


FIXME: foto aerea?/?
.. image:: /images/budapest-frio1.jpeg   # FIXME
    :alt: El auto tapado de hielo, ese frío hacía   # FIXME
    :target: url!!   # FIXME


FIXME: cerrar

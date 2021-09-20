.. title: El futuro del empaquetado en Python
.. date: 2021-01-19 18:01:00
.. tags: Python, packaging, PyCamp, distribución, proyecto

Ok, ok, es un poco pretensioso mi título, pero es que estoy **muy** contento de anunciar la primera liberación de un proyecto que maduró en mi cabeza por unos meses y ya armé una prueba de concepto.

En verdad es más que una prueba de concepto. Está funcionando todo lo que quería, aunque le faltan algunas funcionalidades básicas para lo que yo quería, tests, mejorar el código etc. Podemos decir que está en estado "alfa".

Pero aún versión alfa y todo, ya está publicado: `PyEmpaq v0.2 <FIXME pypi>`_.

.. image:: /images/logo-pyempag.png  FIXME
    :alt: El empaquetado hecho fácil
    :target: https://github.com/facundobatista/pyempaq/

En `la página del proyecto <https://github.com/facundobatista/pyempaq/>`_ está todo super explicado, pero la idea base es que con PyEmpaq se puede meter todo el proyecto (código fuente, imágenes necesarias, etc.) en un sólo archivo ``.pyz``, que termina siendo el único archivo que hay que distribuir.

Exactamente como vemos en este videito:

FIXME

Luego, la persona que recibe o se baja ese archivo, lo único que tiene que hacer es ejecutarlo con Python. La magia de PyEmpaq hará que (la primera vez solamente) ese archivo se expanda en algún lugar piola, se instalen las dependencias necesarias, y finalmente se ejecute el programa indicado.

Lo podemos ver en este otro videito:

FIXME

Es muy multiplataforma: se puede empaquetar estando en Linux, Mac o Windows, y ese `.pyz` resultante funcionará sin problema en esos sistemas operativos. Incluso cruzándolos: podemos empaquetar en Linux y ejecutarlo en Windows, etc.

Para mostrar todo el potencial, armé tres ejemplitos para que cualquiera pueda probar que el empaquetado funciona:

FIXME
- in a terminal: a very small pure terminal example (this, of course, needs to be run in a terminal)
- a game: a simple game using the Python Arcade library (actually, it's the example #6 from their tutorial)
- desktop app: a full-fledged desktop application using PyQt5 (this Encuentro app)
https://github.com/facundobatista/pyempaq/blob/main/examples/simple-command-line.pyz?raw=True
https://github.com/facundobatista/pyempaq/blob/main/examples/arcade-game.pyz?raw=True
https://github.com/facundobatista/pyempaq/blob/main/examples/desktop-qt-app.pyz?raw=True

Como pueden ver `en los issues <https://github.com/facundobatista/pyempaq/issues>`_ le falta bastante laburo para que se lo pueda considerar "estable", pero ya llegará. Por lo pronto lo voy a llevar al `PyCamp de Noviembre <FIXME>`_.

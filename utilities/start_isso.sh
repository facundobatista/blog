#!/bin/sh

export ISSO_SETTINGS="/home/facundo/blog/blog/isso.cfg"
issovenv/bin/gunicorn -b localhost:8732 -w 4 --preload isso.run

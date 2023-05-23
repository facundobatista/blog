#!/bin/sh

cd ~/blog/blog/
git pull
cd site
fades -r ../requirements.txt -x nikola build
cd output/
rsync -t -r --inplace * ~/blog/www


fades -r requirements.txt -x isso -c isso.cfg &
cd site
fades -r ../requirements.txt -x nikola build
fades -r ../requirements.txt -x nikola serve -b
cd ..
#!/usr/bin/env bash
source nikola/bin/activate
cd site
nikola build -a
nikola serve -b


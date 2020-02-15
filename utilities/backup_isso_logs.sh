#!/bin/bash

# set up once a week in a crontab like
#   SSH_AUTH_SOCK=`ls -1 /tmp/ssh-*/agent.*` /home/facundo/sistema/backup_isso_logs.sh

echo Starting...
scp onionsky:blog/issodb/isso-comments.sqlite /massive/backup/
xz /massive/backup/isso-comments.sqlite
mv /massive/backup/isso-comments.sqlite.xz /massive/backup/isso-comments-$(date +%Y%m%d).sqlite.xz
echo Done.

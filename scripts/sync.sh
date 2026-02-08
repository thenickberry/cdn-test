#!/bin/bash

HOSTS=$(egrep $1 ~/.ssh/config | awk '{ print $2 }' | sort)

while true; do
    clear
    date
    echo
    for HOST in $HOSTS; do
        mkdir -p ../nodes/${HOST}/
        echo $HOST
        rsync -az --info=stats -e 'ssh -F /home/nberry/.ssh/config' ${HOST}:[0-9]* ../nodes/${HOST}/
        echo
    done
    echo
    echo sleeping...
    echo
    sleep 300
done

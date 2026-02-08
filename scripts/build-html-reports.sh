#!/bin/bash

HOSTS=$(egrep linode ~/.ssh/config | awk '{ print $2 }' | sort)
INTERVALS="30 600 1800 3600 21600 43200 86400 259200 604800"
PROVIDERS="akam fstl ecst lnkd"
while true; do
    /usr/bin/clear
    /bin/date;
    mkdir -p /domains/nickberry/cdn-test/new;
    for HOST in $HOSTS; do
        echo $HOST
        for INTERVAL in $INTERVALS; do
            echo -n "- $INTERVAL"
            for PROVIDER in $PROVIDERS; do
                PATH=nodes/${HOST}/${INTERVAL}/${PROVIDER};
                if [ -e $PATH ]; then
                    ./html_report.py $PATH;
                    echo -n "  $PROVIDER"
                fi
            done
            echo
        done
    done
    /bin/mv *.html ./htdocs/
    echo
    echo "... sleeping ..."
    echo
    /bin/sleep 300
done

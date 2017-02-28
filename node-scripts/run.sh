#!/bin/bash

URL="mpr/mpr/shrink_100_100/p/4/005/096/365/3336c5b.jpg"
DOMAIN_CODES="akam llnw fstl ecst"

CURL=/usr/bin/curl
TEE=/usr/bin/tee
CURL_ARGS="-v -s -o/dev/null -w @curl-format.txt"

INTERVAL=$1
if [ -z $INTERVAL ]; then
  echo
  echo "[ERROR] Specify an interval"
  echo
  exit
fi

GLOBALSTAMP=$(date +%s)
TESTID="${INTERVAL}_${GLOBALSTAMP}"

echo $TESTID

while true; do
    for DOMAIN_CODE in $DOMAIN_CODES; do
        STAMP=$(date +%s)
        date -d @${STAMP}
        mkdir -p $INTERVAL/$DOMAIN_CODE
        FILE="$INTERVAL/$DOMAIN_CODE/$STAMP"
        DOMAIN="media-${DOMAIN_CODE}.licdn.com"
        URN="https://$DOMAIN/$URL?t=$TESTID"
        CMD="$CURL $CURL_ARGS $URN"
        #echo $CMD
        if [ $DOMAIN_CODE = "akam" ]; then
            $CMD -H 'Pragma: akamai-x-cache-on' |& $TEE $FILE
        elif [ $DOMAIN_CODE = "llnw" ]; then
            $CMD -H 'X-LDebug: 1' |& $TEE $FILE
        else
            $CMD |& $TEE $FILE
        fi
        echo
    done
    sleep $INTERVAL
done

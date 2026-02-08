#!/bin/bash

egrep -q $1 ~/.ssh/config
if [ $? != 0 ]; then
    echo
    echo "ERROR: '$1' missing from ~/.ssh/config"
    echo
    exit 1
fi
scp node-scripts/.screenrc $1:
scp node-scripts/run.sh node-scripts/curl-format.txt node-scripts/start.sh $1:
ssh $1 "./start.sh"

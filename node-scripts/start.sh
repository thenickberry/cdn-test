#!/bin/bash

screen -dmS cdn1 bash -c "./run.sh 30"
screen -dmS cdn2 bash -c "./run.sh 600"
screen -dmS cdn3 bash -c "./run.sh 1800"
screen -dmS cdn4 bash -c "./run.sh 3600"
screen -dmS cdn5 bash -c "./run.sh 21600"
screen -dmS cdn6 bash -c "./run.sh 43200"
screen -dmS cdn7 bash -c "./run.sh 86400"
screen -dmS cdn8 bash -c "./run.sh 259200"
screen -dmS cdn9 bash -c "./run.sh 604800"

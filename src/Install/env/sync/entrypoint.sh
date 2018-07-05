#!/usr/bin/env bash

echo "Press [CTRL+C] to stop.."
while true
do
    unison -auto -batch -silent -watch
    sleep 1
done
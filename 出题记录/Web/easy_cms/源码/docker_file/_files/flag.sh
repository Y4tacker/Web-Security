#!/bin/sh

echo $FLAG > /flagg

export FLAG=not_flag
FLAG=not_flag

rm -f /flag.sh
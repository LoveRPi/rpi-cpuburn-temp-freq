#!/bin/bash

if [ -z "$1" ]; then
	echo "Please specify an output file."
	exit 1
fi

if [ -e "$1" ]; then
	echo "Output file already exists."
	exit 1
fi

php -f collect.php "$1"

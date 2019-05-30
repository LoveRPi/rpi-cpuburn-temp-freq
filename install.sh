#!/bin/bash

sudo apt-get install git build-essential php-cli

git clone https://github.com/ssvb/cpuburn-arm.git

gcc cpuburn-arm/cpuburn-a53.S -o cpuburn-arm/cpuburn-a53



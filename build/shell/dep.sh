#!/bin/bash
#
# Setup the the box. This runs as root

apt-get -y clean
apt-get -y update

apt-get -y install curl
apt-get -y install software-properties-common
add-apt-repository -y ppa:ansible/ansible
apt-get -y clean
apt-get -y update
apt-get install -y --force-yes ansible

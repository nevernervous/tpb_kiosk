#!/usr/bin/env bash

printf "\n\tTPB: copying build to disk...\n\n"
# copy build files to disk
sudo mkdir -p /tmp/tpb/
unzip -o -qq ./latestbuild.zip -d /tmp/tpb/

sudo cp -R /tmp/tpb/latestbuild/www/* /var/www/html

printf "\n\tTPB: adding TPB site to apache...\n\n"
# configure apache
sudo mv /etc/apache2/mods-enabled/dir.conf /etc/apache2/mods-enabled/dir.conf.original
sudo cp ./config/apache/dir.conf /etc/apache2/mods-enabled/dir.conf
sudo cp ./config/apache/tpb.conf /etc/apache2/sites-available/tpb.conf
sudo a2ensite tpb
sudo a2dissite 000-default
#!/usr/bin/env bash

printf "\n\tTPB: copying build to disk...\n\n"
# copy build files to disk
sudo mkdir -p /tmp/tpb/
unzip -o -qq ./latestbuild.zip -d /tmp/tpb/

# substitute staging host for localhost where found in sql dump
sed -ie 's/tpb.waaark.dev/the.peak.beyond/g' /tmp/tpb/latestbuild/sql/tpb_waaark_dev.sql
# copy source DB
mysql -u tpb --password='tpb2017' the_peak_beyond < /tmp/tpb/latestbuild/sql/tpb_waaark_dev.sql
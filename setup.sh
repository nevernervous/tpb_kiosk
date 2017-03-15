#!/usr/bin/env bash

# Don't prompt for input from user during package installations
export DEBIAN_FRONTEND=noninteractive;

# move build files to disk
unzip -o -qq ./latestbuild.zip -d /tmp/tpb/

sudo apt update -y -q
sudo apt upgrade -y -q

# setup TPB admin and kiosk users
#admin_user_not_exists=$(id -u tpb > /dev/null 2>&1; echo $?)
#if ${admin_user_not_exists};
#    then
#        echo "creating admin user"
#        echo "TPB2dFuture" | passwd "tpb" --stdin
#    else
#        echo "admin user already exists"
#fi

#kiosk_user_not_exists=$(id -u kiosk > /dev/null 2>&1; echo $?)
#if ${kiosk_user_not_exists};
#    then
#        echo "creating kiosk user"
#    else
#        echo "kiosk user already exists"
#fi

sudo adduser "tpb" --gecos ""
sudo adduser "tpb" sudo
sudo adduser --disabled-password --gecos "" "kiosk"

# install Apache
sudo apt-get install -y -q apache2

# copy apache config into place
sudo mv /etc/apache2/apache2.conf /etc/apache2/apache2.conf.old
sudo cp ./config/apache/apache2.conf /etc/apache2/apache2.conf
sudo systemctl restart apache2

# enable rewrite for wordpress
sudo a2enmod rewrite
sudo service apache reload

# modify firewall to allow traffic thru Apache on 80 and 443 (todo: is this necessary?)
# sudo ufw allow in "Apache Full"

# install MySQL
sudo -E apt-get -q -y install mysql-server
#todo: secure settings for the MySQL DB should be determined and set up here
sudo mysql -u root mysql < ./config/mysql-setup.sql

# copy source DB
# substitute staging host for localhost where found in sql dump
sed -ie 's/tpb.waaark.dev/the.peak.beyond/g' /tmp/tpb/latestbuild/sql/tpb_waaark_dev.sql
mysql -u tpb --password='tpb2017' the_peak_beyond < /tmp/tpb/latestbuild/sql/tpb_waaark_dev.sql

# set up host to be 'the.peak.beyond' cause that's easy and kinda neat
sudo sed -i "2 a 127.0.1.11    the.peak.beyond" /etc/hosts

# install php
sudo apt-get install -y -q php phpmyadmin libapache2-mod-php php-mcrypt php-mysql php-curl php-gd php-mbstring php-gettext php-xml php-xmlrpc
sudo phpenmod mcrypt
sudo phpenmod mbstring
sudo systemctl restart apache2

# configure apache
sudo mv /etc/apache2/mods-enabled/dir.conf /etc/apache2/mods-enabled/dir.conf.original
sudo cp ./config/apache/dir.conf /etc/apache2/mods-enabled/dir.conf
sudo cp ./config/apache/tpb.conf /etc/apache2/sites-available/tpb.conf
sudo a2ensite tpb
sudo service apache2 reload
sudo mv /etc/apache2/apache2.conf /etc/apache2/apache2.conf.original
sudo cp ./config/apache/apache2.conf /etc/apache2/apache2.conf

# move WP files to apache serving directory
sudo cp -r /tmp/tpb/latestbuild/www/ /var/www/html

# move .htaccess to apache dir
sudo cp ./config/.htaccess /var/www/html

# modify configuration WP configuration
sed -ie "s/define('DB_USER', 'root');/define('DB_USER', 'tpb');/g" /var/www/html/wp-config.php
sed -ie "s/define('DB_PASSWORD', '');/define('DB_PASSWORD', 'tpb2017');/g" /var/www/html/wp-config.php

# configure MySQL permissions
sudo chown -R tpb:www-data /var/www/html
sudo find /var/www/html -type d -exec chmod g+s {} \;
sudo chmod g+w /var/www/html/wp-content
sudo chmod -R 777 /var/www/html/wp-content #for WP Rocket

# install browser for kiosk and other useful things
sudo apt install -y -q chromium-browser unclutter xdotool

# install multitouch
sudo apt-get -y -q install geis-tools
sudo apt-get -y -q install touchegg
sudo cp ./config/.xprofile /home/tpb
sudo chown tpb /home/tpb/.xprofile
sudo cp ./config/.xprofile /home/kiosk/.xprofile
sudo chown kiosk /home/kiosk/.xprofile

## set up thermal printer


# needs java runtime
sudo apt-get -y -q install default-jre

# add TPB kiosk launch task
# todo: configure launch user profile stuff
sudo mkdir -p /etc/lightdm/
sudo cp ./config/lightdm/lightdm.conf /etc/lightdm/lightdm.conf

sudo mkdir -p /etc/lightdm/lightdm.conf.d
sudo cp ./config/lightdm/50-myconfig.conf /etc/lightdm/lightdm.conf.d/50-myconfig.conf

# configure boot-to-browser
sudo mkdir -p /home/kiosk/.config/autostart
sudo cp ./config/lightdm/kiosk.desktop /home/kiosk/.config/autostart/kiosk.desktop

# install browser boot script
sudo rm /home/kiosk/kiosk.sh
sudo cp ./kiosk.sh /home/kiosk/kiosk.sh
sudo chmod u+x /home/kiosk/kiosk.sh
sudo chown kiosk /home/kiosk/kiosk.sh

#todo: install and configure teamviewer
## install teamViewer
#wget https://download.teamviewer.com/download/teamviewer_i386.deb -O /tmp/tpb/teamviewer.deb
#sudo -E apt-get -q -y install /tmp/tpb/teamviewer.deb
#
## update TeamViewer startup config to wait for network to boot
#sed -i "4 a After=time-sync.target" /etc/systemd/system/teamviewerd.service
#sed -i "4 a After=network-online.target" /etc/systemd/system/teamviewerd.service;
#sudo service teamviewerd reload
#sudo service teamviewerd restart
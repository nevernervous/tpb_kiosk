#!/usr/bin/env bash
sudo apt update
sudo apt upgrade

# TODO Set debian noninteractive package management

# setup TPB admin and kiosk users
admin_user_not_exists=$(id -u tpb > /dev/null 2>&1; echo $?)
if ${admin_user_not_exists};
    then
        echo "creating admin user"
        sudo adduser "tpb" --gecos ""
        sudo adduser "tpb" sudo
        echo "TPB2dFuture" | passwd "tpb" --stdin
    else
        echo "admin user already exists"
fi

kiosk_user_not_exists=$(id -u kiosk > /dev/null 2>&1; echo $?)
if ${kiosk_user_not_exists};
    then
        echo "creating kiosk user"
        sudo adduser --disabled-password --gecos "" "kiosk"
    else
        echo "kiosk user already exists"
fi
# install Apache
sudo apt install apache2

# copy apache config into place
sudo mv /etc/apache2/apache2.conf /etc/apache2/apache2.conf.old
sudo cp ./config/apache2.conf /etc/apache2/apache2.conf
sudo systemctl restart apache2

# enable rewrite for wordpress
sudo a2enmod rewrite

# modify firewall to allow traffic thru Apache on 80 and 443 (todo: is this necessary?)
sudo ufw allow in "Apache Full"

# install MySQL
sudo apt-get install mysql-server
sudo mysql_secure_installation

# copy source DB
mv ./latestbuild/sql/tpb_waaark_dev.sql ~/tpb_import.sql
mysql < ~/tpb_import.sql

# configure MySQL
sudo chown -R tpb:www-data /var/www/html
sudo find /var/www/html -type d -exec chmod g+s {} \;
sudo chmod g+w /var/www/html/wp-content

# install php
sudo apt-get install php libapache2-mod-php php-mcrypt php-mysql php-curl php-gd php-mbstring php-mcrypt php-xml php-xmlrpc

# configure php server
sudo mv /etc/apache2/mods-enabled/dir.conf /etc/apache2/mods-enabled/dir.conf.old
sudo cp ./config/dir.conf /etc/apache2/mods-enabled/dir.conf

# install teamViewer
wget https://download.teamviewer.com/download/teamviewer_i386.deb -O ~/teamviewer.deb
sudo apt install ~/teamviewer.deb

# update TeamViewer startup config to wait for network to boot
sed "4 a After=time-sync.target" /etc/systemd/system/teamviewerd.service
sed "4 a After=network-online.target" /etc/systemd/system/teamviewerd.service;
sudo service teamviewerd reload
sudo service teamviewerd restart

# move WP files to apache serving directory
sudo cp -r ./latestbuild/www/* /var/www/html

# install browser for kiosk and other useful things
sudo apt install chromium-browser unclutter xdotool

# add TPB kiosk launch task
# todo: configure launch user profile stuff
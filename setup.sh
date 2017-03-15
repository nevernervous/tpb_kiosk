#!/usr/bin/env bash
printf "\n\n\t\tTPB KIOSK INSTALL\n"

# Don't prompt for input from user during package installation / mgmt
export DEBIAN_FRONTEND=noninteractive;

printf "\n\n\t\tTPB: removing extra packages...\n\n"
# Ubuntu GNOME comes with some stuff we don't need
sudo apt-get -y -qq remove --purge libreoffice*
sudo apt-get -y -qq clean
sudo apt-get -y -qq autoremove

printf "\n\n\t\tTPB: copying build to disk...\n\n"

# copy build files to disk
unzip -o -qq ./latestbuild.zip -d /tmp/tpb/

# copy WP files to apache serving directory
sudo mkdir -p /var/www/html
sudo cp -r /tmp/tpb/latestbuild/www/* /var/www/html

# copy .htaccess to apache dir
sudo cp ./config/.htaccess /var/www/html

printf "\n\n\tTPB: updating system packages...\n\n"
sudo apt-get -y -qq update
sudo apt-get -y -qq upgrade
sudo apt-get -y -qq autoremove

# install Apache
sudo apt-get -y -qq install apache2

printf "\n\n\tTPB: creating users...\n\n"
sudo adduser "tpb" --gecos ""
sudo adduser "tpb" sudo
sudo adduser --disabled-password --gecos "" "kiosk"

printf "\n\n\tTPB: updating apache config...\n\n"
# copy apache config into place
sudo mv /etc/apache2/apache2.conf /etc/apache2/apache2.conf.old
sudo cp ./config/apache/apache2.conf /etc/apache2/apache2.conf
sudo systemctl restart apache2
# enable rewrite for wordpress
sudo a2enmod rewrite
sudo service apache reload

# modify firewall to allow traffic thru Apache on 80 and 443 (todo: is this necessary?)
# sudo ufw allow in "Apache Full"
printf "\n\n\tTPB: setting up MySQL DB for WordPress...\n\n"
# install MySQL
sudo -E apt-get -qq -y install mysql-server
#todo: secure settings for the MySQL DB should be determined and set up here
sudo mysql -u root mysql < ./config/mysql-setup.sql

# copy source DB
printf "\n\n\tTPB: importing TPB DB from dump...\n\n"
# substitute staging host for localhost where found in sql dump
sed -ie 's/tpb.waaark.dev/the.peak.beyond/g' /tmp/tpb/latestbuild/sql/tpb_waaark_dev.sql
mysql -u tpb --password='tpb2017' the_peak_beyond < /tmp/tpb/latestbuild/sql/tpb_waaark_dev.sql

printf "\n\n\tTPB: configuring local url \`the.peak.beyond\`...\n\n"
# set up host to be 'the.peak.beyond' cause that's easy and kinda neat
sudo sed -i "2 a 127.0.1.11    the.peak.beyond" /etc/hosts

printf "\n\n\tTPB: installing php...\n\n"
# install php
sudo apt-get install -y -qq php phpmyadmin libapache2-mod-php php-mcrypt php-mysql php-curl php-gd php-mbstring php-gettext php-xml php-xmlrpc
sudo phpenmod mcrypt
sudo phpenmod mbstring
sudo systemctl restart apache2

printf "\n\n\tTPB: adding TPB site to apache...\n\n"
# configure apache
sudo mv /etc/apache2/mods-enabled/dir.conf /etc/apache2/mods-enabled/dir.conf.original
sudo cp ./config/apache/dir.conf /etc/apache2/mods-enabled/dir.conf
sudo cp ./config/apache/tpb.conf /etc/apache2/sites-available/tpb.conf
sudo a2ensite tpb
sudo service apache2 reload
sudo mv /etc/apache2/apache2.conf /etc/apache2/apache2.conf.original
sudo cp ./config/apache/apache2.conf /etc/apache2/apache2.conf

printf "\n\n\tTPB: updating WP configuration with new DB settings...\n\n"
# modify WP configuration
sudo sed -ie "s/define('DB_USER', 'root');/define('DB_USER', 'tpb');/g" /var/www/html/wp-config.php
sudo sed -ie "s/define('DB_PASSWORD', '');/define('DB_PASSWORD', 'tpb2017');/g" /var/www/html/wp-config.php

printf "\n\n\tTPB: updating DB settings...\n\n"
# configure MySQL permissions
sudo chown -R tpb:www-data /var/www/html
sudo find /var/www/html -type d -exec chmod g+s {} \;
sudo chmod g+w /var/www/html/wp-content
sudo chmod -R 777 /var/www/html/wp-content #for WP Rocket

printf "\n\n\tTPB: installing browser etc...\n\n"
# install browser for kiosk and other useful things
sudo apt-get -qq -y install -y chromium-browser unclutter xdotool

printf "\n\n\tTPB: setting up multitouch settings...\n\n"
# install multitouch
sudo apt-get -qq -y install geis-tools
sudo apt-get -qq -y  install touchegg
sudo cp ./config/.xprofile /home/tpb
sudo chown tpb /home/tpb/.xprofile
sudo cp ./config/.xprofile /home/kiosk/.xprofile
sudo chown kiosk /home/kiosk/.xprofile

printf "\n\n\tTPB: configuring kiosk user auto-login...\n\n"
# auto login in GDM display manager
sudo sed -i 's/#  AutomaticLoginEnable = true/AutomaticLoginEnable = true/g' /etc/gdm3/custom.conf
sudo sed -i 's/#  AutomaticLogin = user1/AutomaticLogin = kiosk/g' /etc/gdm3/custom.conf

sudo groupadd nopasswdlogin
sudo sed -i '1 a auth sufficient pam_succeed_if.so user ingroup nopasswdlogin' /etc/pam.d/gdm-password

# add kiosk user to no password required group
sudo usermod -a -G nopasswdlogin kiosk
sudo passwd -d kiosk

# configure boot-to-browser
sudo mkdir -p /home/kiosk/.config/autostart
sudo cp ./config/gdm/kiosk.desktop /home/kiosk/.config/autostart/kiosk.desktop

# install browser boot script
sudo rm /home/kiosk/kiosk.sh
sudo cp ./kiosk.sh /home/kiosk/kiosk.sh
sudo chmod u+x /home/kiosk/kiosk.sh
sudo chown kiosk /home/kiosk/kiosk.sh

printf "\n\n\tTPB: setting up thermal printer...\n\n"

##
# set up thermal printer
##
# package requirements for printer
sudo apt-get -y -qq install openjdk-8-jdk ant nsis makeself

##
# install and configure teamViewer
##
printf "\n\n\tTPB: installing team...\n\n"

wget https://download.teamviewer.com/download/teamviewer_i386.deb -O /tmp/tpb/teamviewer.deb
sudo -E apt-get -qq -y install /tmp/tpb/teamviewer.deb
#
## update TeamViewer startup config to wait for network to boot
#sed -i "4 a After=time-sync.target" /etc/systemd/system/teamviewerd.service
#sed -i "4 a After=network-online.target" /etc/systemd/system/teamviewerd.service;
#sudo service teamviewerd reload
#sudo service teamviewerd restart
#!/usr/bin/env bash

sudo printf "\n\t***************************\n\t\tTPB KIOSK INSTALL\n\t***************************\n"

if sudo -n true 2>/dev/null; then
    printf "\n\tTPB: Access granted."
else
    printf "\n\tTPB: Access denied... bad password? \n\tExiting now."
    exit
fi

# Don't prompt for input from user during package installation / mgmt
export DEBIAN_FRONTEND=noninteractive;

printf "\n\tTPB: removing extra packages...\n\n"
# Ubuntu GNOME comes with some stuff we don't need
sudo apt-get -y -qq remove --purge libreoffice*
sudo apt-get -y -qq clean
sudo apt-get -y -qq autoremove

printf "\n\tTPB: updating system packages...\n\n"
sudo apt-get -y -qq update
sudo apt-get -y -qq upgrade
sudo apt-get -y -qq autoremove

# install some required packages
sudo apt-get -y -qq install curl unzip

printf "\n\tTPB: copying build to disk...\n\n"

# copy build files to disk
# clean-up old install if present
sudo rm -rf /tmp/tpb/
sudo rm -rf /var/tmp/tpb/
# create new dir and copy files
sudo mkdir -p /tmp/tpb/
#unzip -o -qq ./latestbuild.zip -d /tmp/tpb/
sudo rsync -avh ./latestbuild /tmp/tpb/
sudo cp -r ./drivers /tmp/tpb/

# copy WP files to apache serving directory
sudo mkdir -p /var/www/html
sudo cp -r /tmp/tpb/latestbuild/www/* /var/www/html

# copy .htaccess to apache dir
sudo cp ./config/.htaccess /var/www/html

# install Apache
sudo apt-get -y -qq install apache2

printf "\n\tTPB: creating users...\n\n"
sudo adduser "tpb" --gecos ""
sudo adduser "tpb" sudo
sudo adduser --disabled-password --gecos "" "kiosk"

printf "\n\tTPB: updating apache config...\n\n"
# copy apache config into place
sudo mv /etc/apache2/apache2.conf /etc/apache2/apache2.conf.old
sudo cp ./config/apache/apache2.conf /etc/apache2/apache2.conf
sudo systemctl restart apache2
# enable rewrite for wordpress
sudo a2enmod rewrite
printf "\n\tTPB: reloading apache...\n\n"
sudo service apache2 reload

# modify firewall to allow traffic thru Apache on 80 and 443 (todo: is this necessary?)
# sudo ufw allow in "Apache Full"
printf "\n\tTPB: setting up MySQL DB for WordPress...\n\n"
# install MySQL
sudo -E apt-get -qq -y install mysql-server
#todo: secure settings for the MySQL DB should be determined and set up here
sudo mysql -u root mysql < ./config/mysql-setup.sql

printf "\n\tTPB: importing TPB DB from dump...\n\n"
# substitute staging host for localhost where found in sql dump
sudo sed -ie 's/tpb.waaark.dev/the.peak.beyond/g' /tmp/tpb/latestbuild/sql/tpb_waaark_dev.sql
# copy source DB
sudo mysql -u tpb --password='tpb2017' the_peak_beyond < /tmp/tpb/latestbuild/sql/tpb_waaark_dev.sql

# set up host to be 'the.peak.beyond' cause that's easy and kinda neat
printf "\n\tTPB: configuring local url \`the.peak.beyond\`...\n\n"
sudo sed -i "2 a 127.0.1.11    the.peak.beyond" /etc/hosts

printf "\n\tTPB: installing php...\n\n"
# install php
sudo apt-get install -y -qq php libapache2-mod-php php-mcrypt php-mysql php-curl php-gd php-mbstring php-gettext php-xml php-xmlrpc
sudo phpenmod mcrypt
sudo phpenmod mbstring
printf "\n\tTPB: reloading apache...\n\n"
sudo systemctl restart apache2

printf "\n\tTPB: adding TPB site to apache...\n\n"
# configure apache
sudo mv /etc/apache2/mods-enabled/dir.conf /etc/apache2/mods-enabled/dir.conf.original
sudo cp ./config/apache/dir.conf /etc/apache2/mods-enabled/dir.conf
sudo cp ./config/apache/tpb.conf /etc/apache2/sites-available/tpb.conf
sudo a2ensite tpb
sudo a2dissite 000-default

printf "\n\tTPB: reloading apache...\n\n"
sudo service apache2 reload
sudo mv /etc/apache2/apache2.conf /etc/apache2/apache2.conf.original
sudo cp ./config/apache/apache2.conf /etc/apache2/apache2.conf

printf "\n\tTPB: updating WP configuration with new DB settings...\n\n"
# modify WP configuration
sudo sed -ie "s/define('DB_USER', 'root');/define('DB_USER', 'tpb');/g" /var/www/html/wp-config.php
sudo sed -ie "s/define('DB_PASSWORD', '');/define('DB_PASSWORD', 'tpb2017');/g" /var/www/html/wp-config.php

sudo service apache2 start

printf "\n\tTPB: updating DB settings...\n\n"
# configure MySQL permissions
sudo chown -R tpb:www-data /var/www/html
sudo find /var/www/html -type d -exec chmod g+s {} \;
sudo chmod g+w /var/www/html/wp-content
sudo chmod -R 777 /var/www/html/wp-content #for WP Rocket

printf "\n\tTPB: installing browser etc...\n\n"
# install browser for kiosk and other useful things
sudo apt-get -qq -y install chromium-browser unclutter xdotool

printf "\n\tTPB: setting up multitouch settings...\n\n"
# install multitouch
sudo apt-get -qq -y install geis-tools touchegg
sudo cp ./config/.xprofile /home/tpb/.xprofile
sudo chown tpb /home/tpb/.xprofile

sudo cp ./config/.xprofile /home/kiosk/.xprofile
sudo chown kiosk /home/kiosk/.xprofile

sudo xhost +SI:localuser:kiosk

printf "\n\tTPB: configuring kiosk user auto-login...\n\n"

# install missing requirements for Intel graphics drivers
sudo apt-get -qq -y install xserver-xorg-legacy gdm3

# auto login in GDM display manager
sudo sed -i 's/#  TimedLoginEnable = true/TimedLoginEnable = true/g' /etc/gdm3/custom.conf
sudo sed -i 's/#  TimedLogin = user1/TimedLogin = kiosk/g' /etc/gdm3/custom.conf
sudo sed -i 's/#  TimedLoginDelay = 10/TimedLoginDelay = 4/g' /etc/gdm3/custom.conf

sudo groupadd nopasswdlogin
sudo sed -i '1 a auth sufficient pam_succeed_if.so user ingroup nopasswdlogin' /etc/pam.d/gdm-password

# add kiosk user to no password required group
sudo usermod -a -G nopasswdlogin kiosk
sudo passwd -d kiosk

# configure boot-to-browser
sudo mkdir -p /home/kiosk/.config/autostart
sudo cp ./config/gdm/kiosk.desktop /home/kiosk/.config/autostart/kiosk.desktop
sudo chmod +x /home/kiosk/.config/autostart/kiosk.desktop
# add shortcut to desktop
sudo mkdir -p /home/kiosk/Desktop
sudo chown kiosk:kiosk /home/kiosk/Desktop
sudo ln -sf /home/kiosk/.config/autostart/kiosk.desktop /home/kiosk/Desktop/kiosk.desktop

# install browser boot script
sudo rm /home/kiosk/kiosk.sh
sudo cp ./scripts/kiosk.sh /home/kiosk/kiosk.sh
sudo chmod 755 /home/kiosk/kiosk.sh

# make sure everything in kiosk home is owned by kiosk
sudo chown -R kiosk:kiosk ~kiosk

##
# set up thermal printer
##
printf "\n\tTPB: setting up thermal printer...\n\n"
# package requirements for printer
sudo apt-get -y -qq install openjdk-8-jdk
sudo apt-get -y -qq install ant nsis makeself libnss3-tools
sudo cp /tmp/tpb/drivers/printer/qz-tray-2.0.3.run ~/qz.run
sudo chmod +x ~/qz.run
sudo ~/qz.run

# install printer drivers from OCOMInc.com
# using linux drivers for printer type: OCPP 809
sudo chmod +x /tmp/tpb/drivers/printer/install64
sudo /tmp/tpb/drivers/printer/install64

# install printer certificate
sudo certutil -d sql:$HOME/.pki/nssdb -A -t TC -n  "QZ Industries, LLC" -i /opt/qz-tray/auth/qz-tray.crt
##
# install and configure teamViewer
##
printf "\n\tTPB: installing TeamViewer...\n\n"

# use downloaded copy of TW
if [ -f teamviewer_i386.deb ]; then 
  sudo cp -p teamviewer_i386.deb /tmp/tpb/teamviewer.deb
fi
# download otherwise
if [ ! -f /tmp/tpb/teamviewer.deb ]; then 
  sudo wget https://download.teamviewer.com/download/teamviewer_i386.deb -O /tmp/tpb/teamviewer.deb
fi 
sudo -E apt-get -qq -y install /tmp/tpb/teamviewer.deb
# sudo cp ./config/teamviewer/teamviewerd.service /etc/systemd/system/teamviererd.service
# sudo initctl reload-configuration

printf "\n\tTPB: Please open TeamViewer, accept license and set up your account, \n
make sure to click the 'Start TeamViewer with system' button and the two below it, \n
then *quit* TeamViewer to finish setup.\n\n"

sudo teamviewer daemon enable

#
### update TeamViewer startup config to wait for network to boot
#sudo sed -i "4 a After=time-sync.target" /etc/systemd/system/teamviewerd.service
#sudo sed -i "4 a After=network-online.target" /etc/systemd/system/teamviewerd.service;

sudo systemctl enable teamviewerd.service
sudo systemctl daemon-reload
sudo systemctl restart teamviewerd


# run sync setup
cd scripts
./sync-setup.sh

printf "\n\t***************************\n\t\tTPB KIOSK INSTALL COMPLETE!\n\t***************************\n"


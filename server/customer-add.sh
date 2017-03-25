#!/bin/bash

# this script will add a customer

# check where we are running from
if [ ! -d ../server ]; then
    echo "please run this script from the 'server' directory"
    exit 0
fi

# this is used trough the script and will control the following:
#   user name
#   site document root
#   php-fpm pool
SITE=""

# ask customer subdomain/site name
while true; do
if [ "$SITE" == "" ]; then
    echo "input site (subdomain) name and press Enter"
    read SITE
    if [ "$SITE" != "" ]; then
        break;
    fi
fi
done

# confirm name correct
echo "make sure the site name is correct"
read -p "Continue (y/n)?" choice
if [[ ! $choice =~ ^[Yy]$ ]]
then
    echo 'exit...'; exit 0;
fi

# create user
echo "create user: $SITE"
adduser $SITE

# ssh stuff
echo "create ssh placeholder"
mkdir /home/$SITE/.ssh
chmod 700 /home/$SITE/.ssh
touch /home/$SITE/.ssh/authorized_keys
chmod 600 /home/$SITE/.ssh/authorized_keys
chown -R $SITE:$SITE /home/$SITE/.ssh

# -- website --
# create document root
echo "create /var/www/$SITE website document root"
mkdir -p /var/www/$SITE

# assume we have the templates
if [ ! -d ./templates ]; then
    echo "ERROR: templates dir not found! Please run script from server directory. exit..."
    exit 0
fi

# configure site in apache
echo "configure $SITE in apache"
TEMPLATE=./templates/apache/100-site1.conf
FILE="/etc/apache2/sites-available/100-$SITE.conf"

if [ ! -f "$FILE" ]; then 
    sed "s/site1/$SITE/g" $TEMPLATE > $FILE
else 
    echo "file $FILE exits; skip..."
fi

# configure php-fpm pool
echo "configure $SITE php-fpm application pool" 
TEMPLATE=./templates/php-fpm/site1.conf
FILE="/etc/php/7.0/fpm/pool.d/$SITE.conf"

if [ ! -f "$FILE" ]; then 
    sed "s/site1/$SITE/g" $TEMPLATE > $FILE
else 
    echo "file $FILE exits; skip..."
fi

# -- database --
# user with just enough permissions to create users and db
DB_HOST="dbserver.ceal64qdo6z2.us-west-2.rds.amazonaws.com"
DB_USER="usersetup"
DB_PASS="khrNolEmxw034l1"

# create database for site
echo "create database $SITE"
mysql -h $DB_HOST -u $DB_USER -p"$DB_PASS" -e "create database $SITE"

# create db user and grant permissions
WP_PASS=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 16 | head -n 1)

SQL="create user '$SITE'@'%' identified by '$WP_PASS';
grant all on $SITE.* to '$SITE'@'%';
flush privileges;"

# exec
mysql -h $DB_HOST -u $DB_USER -p"$DB_PASS" -e "$SQL"

# -- import --
# import sample db 
DUMP=/tmp/wpdb.sql

# create a copy
cp -p ../latestbuild/sql/tpb_waaark_dev.sql $DUMP
# modify dump
sed -i "s/tpb.waaark.dev/$SITE.thepeakbeyond.com/g" $DUMP

# import dump to database
echo "import database $SITE from $DUMP"
mysql -h $DB_HOST -u $SITE -p"$WP_PASS" $SITE < $DUMP

# copy files from latestbuild to site-document root
echo "copy files to /var/www/$SITE"
rsync -ah ../latestbuild/www/ /var/www/$SITE/

# update wp-config
FILE=/var/www/$SITE/wp-config.php

echo "update wp-config.php"
sed -i.bk \
    -e "s/DB_NAME', '.*'/DB_NAME', '$SITE'/g" \
    -e "s/DB_USER', '.*'/DB_USER', '$SITE'/g" \
    -e "s/DB_PASSWORD', '.*'/DB_PASSWORD', '$WP_PASS'/g" \
    -e "s/DB_HOST', '.*'/DB_HOST', '$DB_HOST'/g" \
    $FILE

# print values
echo "NEW CONFIG:"
cat $FILE | grep 'DB_[N|U|H|P]'

# apply file permissions
echo "apply permissions"
chmod 750 /var/www/$SITE
chown -R $SITE:$SITE /var/www/$SITE

# give www-data group read permissions
echo "update group"
usermod -aG $SITE www-data

# -- apply changes --
# enable site in apache
echo "enable $SITE in apache"
a2ensite 100-$SITE.conf

# reload php-fpm
echo "restart php-fpm"
service php7.0-fpm restart

# reload apache
echo "restart apache"
service apache2 restart

echo "customer setup for $SITE is done."

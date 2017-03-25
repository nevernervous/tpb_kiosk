#!/bin/bash

# this script will delete a customer

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
echo "this will DELETE ALL DATA for a customer!!"
read -p "Continue (y/n)?" choice
if [[ ! $choice =~ ^[Yy]$ ]]
then
    echo 'exit...'; exit 0;
fi

# disable website
echo "disable $SITE in apache"
a2dissite 100-$SITE.conf

# remove configs
rm -f /etc/apache2/sites-available/100-$SITE.conf
rm -f /etc/php/7.0/fpm/pool.d/$SITE.conf

# restart services
echo "restart php-fpm"
service php7.0-fpm restart

# reload apache
echo "restart apache"
service apache2 restart

# delete files
rm -rf /var/www/$SITE/

# delete db 
# user with just enough permissions to create users and db
DB_HOST="dbserver.ceal64qdo6z2.us-west-2.rds.amazonaws.com"
DB_USER="usersetup"
DB_PASS="khrNolEmxw034l1"

# drop database and user for site
echo "drop database $SITE; drop user $SITE"
mysql -h $DB_HOST -u $DB_USER -p"$DB_PASS" -e "drop database $SITE; drop user $SITE"

# remove www-data from group
deluser www-data $SITE

# delete linux  user
userdel -r $SITE

echo "Done"


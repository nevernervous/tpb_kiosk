#!/bin/bash -e 

# this script will connect to the master web server that hosts the wp instances for each customer
# and will sync the whole web install directory for a given customer
# contents of /var/www/SITE will be copied and the WP database
# finaly the local config is modified to point to the local db


# define the name of the customer
# also defines values like:
#   user to login
#   subdomain
#   files location on the server
SITE=site1

# define where the script will reside
DIR=/tmp/tpb

# if we got a name as an argument, use it instead
if [ "$1" != "" ]; then
	SITE="$1"
fi

# if there is a config file, read from it
if [ -f $DIR/site.txt ]; then
	SITE=`cat $DIR/site.txt`;
fi

if [ $SITE == "" ]; then
	echo "can't detect site name. exit..."; exit 0;
fi

echo "CUSTOMER NAME: $SITE"

# ----------------------------------------------------------
# sync files 
# ----------------------------------------------------------
# ! this will delete local files
echo "sync files from $SITE.thepeakbeyond.com"
rsync -avh --progress --delete $SITE@$SITE.thepeakbeyond.com:/var/www/$SITE/ /var/www/html/

# permissions
chown -R www-data: /var/www/html

# ----------------------------------------------------------
# get db
# ----------------------------------------------------------
# read db config from: 
FILE=/var/www/html/wp-config.php
echo "Read server config:"

# print original values
DB_NAME=`cat $FILE | grep DB_NAME | sed "s/'/ /g" | awk '{print $4}'`
DB_USER=`cat $FILE | grep DB_USER | sed "s/'/ /g" | awk '{print $4}'`
DB_PASSWORD=`cat $FILE | grep DB_PASSWORD | sed "s/'/ /g" | awk '{print $4}'`
DB_HOST=`cat $FILE | grep DB_HOST | sed "s/'/ /g" | awk '{print $4}'`

#echo $DB_NAME
#echo $DB_USER
#echo $DB_PASSWORD
#echo $DB_HOST

# get the db on the web server, then copy it across
echo "Get DB from server:"

CMD="cd; mysqldump -q -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME > $DB_NAME.sql && gzip -f $DB_NAME.sql"
#echo $CMD
ssh $SITE@$SITE.thepeakbeyond.com $CMD
scp $SITE@$SITE.thepeakbeyond.com:~/$DB_NAME.sql.gz $DIR/
ls -lh $DIR/$DB_NAME.sql.gz

# ! make sure we have a good file at this point

# ----------------------------------------------------------
# import db
# ----------------------------------------------------------
echo "Import DB:"
zcat $DIR/$DB_NAME.sql.gz | mysql -h localhost -u tpb --password='tpb2017' the_peak_beyond

# ----------------------------------------------------------
# update wp config
# ----------------------------------------------------------
# update wordpress config to match values defined in setup script

echo "Update wp-config.php"
sed -i.bk \
	-e "s|#define('WP_HOME','http://example.com')|define('WP_HOME','http://the.peak.beyond')|g" \
	-e "s|#define('WP_SITEURL','http://example.com')|define('WP_SITEURL','http://the.peak.beyond')|g" \
	\
	-e "s/DB_NAME', '.*'/DB_NAME', 'the_peak_beyond'/g" \
	-e "s/DB_USER', '.*'/DB_USER', 'tpb'/g" \
	-e "s/DB_PASSWORD', '.*'/DB_PASSWORD', 'tpb2017'/g" \
	-e "s/DB_HOST', '.*'/DB_HOST', 'localhost'/g" \
	$FILE

# print values
echo "NEW CONFIG:"
cat $FILE | grep 'DB_[N|U|H|P]\|WP_[H|S]'


echo "Done"

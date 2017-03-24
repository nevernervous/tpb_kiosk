#!/bin/bash -e

# sync
echo "=========== Kiosk Sync setup ==========="
DIR=/tmp/tpb

# read site name
if [ -f "$DIR/site.txt" ]; then
    SITE=`cat $DIR/site.txt`    
    echo "read $SITE name from $DIR/site.txt"
fi

# ask user for site name
while true; do 
if [ "$SITE" == "" ]; then 
    echo "input site name and press Enter"
    read SITE
    if [ "$SITE" != "" ]; then 
        echo $SITE > $DIR/site.txt
        break;
    fi
fi
done

# exit if we don't have a site name at this point
if [ "$SITE" == "" ]; then 
    echo "site name empty. exit..."
    exit 0
fi

echo "setting up sync for site $SITE"
# copy sync script
if [ ! -f $DIR/sync.sh ]; then
    cp -pf sync.sh $DIR/
fi

# install crontab 
echo "installing crontab"
if (! (crontab -l | grep -q sync.sh)); then
	(crontab -l ; echo "0 3 * * * /tmp/tpb/sync.sh $SITE")| crontab -
fi

# allow www-data to run sync without asking for a password
echo "checking sudoers file"
if ( ! grep -q www-data /etc/sudoers ); then
    echo 'www-data ALL=(ALL) NOPASSWD: /tmp/tpb/sync.sh' >> /etc/sudoers
fi

# setup key
if [ ! -f "$HOME/.ssh/id_rsa" ]; then
    echo "setup rsa key. accept defaults..."
    ssh-keygen -t rsa
fi

echo
echo "if adding the ssh key on the web server is necessary"
echo "run this command on the web server (as root):"
KEY=`cat $HOME/.ssh/id_rsa.pub`
echo 
echo "echo '$KEY' >> /home/$SITE/.ssh/authorized_keys"
echo 

read -p "Continue (y/n)?" choice
if [[ ! $choice =~ ^[Yy]$ ]]
then
    echo 'exit...'; exit 0; 
fi


echo "run sync"
$DIR/sync.sh $SITE

# end

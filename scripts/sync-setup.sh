# sync
echo "=========== Kiosk Sync setup ==========="
DIR=/tmp/tpb

# read site name
if [ -f "$DIR/site.txt" ]; then
    SITE=`cat $DIR/site.txt`    
    echo "read $SITE name from $DIR/site.txt"
fi

if [ "$SITE" == "" ]; then 
    echo "input site name and press Enter"
    read SITE
    echo $SITE > $DIR/site.txt
fi

echo "setting up sync for site $SITE"

if [ ! -f "$HOME/.ssh/id_rsa" ]; then
    echo "setup rsa key. accept defaults..."
    ssh-keygen -t rsa
fi
    echo
    echo "if you need to add the ssh key to the server, run this command:"
    KEY=`cat $HOME/.ssh/id_rsa.pub`
    echo "echo '$KEY' >> /home/$SITE/.ssh/authorized_keys"

# copy sync script
if [ ! -f $DIR/sync.sh ]; then
    cp -pf ./scripts/sync.sh $DIR/
fi

# install crontab 
if (! (crontab -l | grep -q sync.sh)); then
	(crontab -l ; echo "0 3 * * * /tmp/tpb/sync.sh $SITE")| crontab -
fi

# allow www-data to run sync without asking for a password
echo "check sudoers file"
if ( ! grep -q www-data /etc/sudoers ); then
    echo 'www-data ALL=(ALL) NOPASSWD: /tmp/tpb/sync.sh' >> /etc/sudoers
fi

echo 
read -p "Continue (y/n)?" choice
if [[ ! $choice =~ ^[Yy]$ ]]
then
    echo 'exit...'; exit 0; 
fi


echo "run sync"
$DIR/sync.sh $SITE

# end
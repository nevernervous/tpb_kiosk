#!/bin/bash

# exit on error
set -e

# this script will update the git repo and then sync a particular site to the latest version of the branch
# only applies to wp files

# get parameters
SITE=$1
BRANCH=$2

# check for branch name from text file and use that if branch not spevified
if [ "$BRANCH" == "" ] && [ -f /var/www/$SITE/branch.txt ]; then
    BRANCH=$(cat /var/www/$SITE/branch.txt)
    echo "found previous branch config: $BRANCH for site $SITE"
fi

if [ "$SITE" == "" ] || [ "$BRANCH" == "" ]; then 
    echo "USAGE: ./site_update.sh site_name branch_name"
    exit 0
fi

# have to be root
if [ $(whoami) != "root" ]; then
    echo "run this script as root. exit..."
    exit 1
fi

# check where are we running from
if [ ! -d .git ]; then
    echo "please run this script from the repo directory"
    exit 1 
fi

# check for user
if ( ! id "$SITE" 2> /dev/null 1>/dev/null ); then
    echo "can't find user $SITE. exit..."
    exit 1 
fi

# check /var/www/exists
if [ ! -d /var/www/$SITE ]; then
    echo "can't find directory /var/www/$SITE for user $SITE"
    exit 1 
fi

# check branch exists
#  // don't check now, will fail if branch not found

# confirm name correct
echo "going to apply branch $BRANCH to $SITE"
read -p "Continue (y/n)?" choice
if [[ ! $choice =~ ^[Yy]$ ]]
then
    echo 'exit...'; exit 0;
fi

echo "update repo"
git pull

# 
echo "available branches"
git branch -a

echo "change branch"
git checkout $BRANCH


# do sync
echo "sync files"
rsync -v -rlt --exclude=wp-config.php ./latestbuild/www/ /var/www/$SITE/

# record branch name
echo "record branch name"
echo "$BRANCH" > /var/www/$SITE/branch.txt

# apply file permissions
echo "apply permissions"
# modify permissions to include editors group
chown -R $SITE:editors /var/www/$SITE
find /var/www/$SITE -type d -exec chmod 775 {} \;
find /var/www/$SITE -type d -exec chmod g+s {} \;
find /var/www/$SITE -type f -exec chmod 664 {} \;
setfacl -Rdm g:editors:rwx /var/www/$SITE
chmod 770 /var/www/$SITE

echo 
echo "done."

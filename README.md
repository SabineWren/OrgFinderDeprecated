# Cognition Corp

Website Database for players to find organizations.

Live hosted at www.cognitioncorp.net

The scraper was built from Siegen's sc-api.

To host the app yousrelf, add and run dependencies. Caveat -- PHP sucks with relative paths, so the scripts in /dbPop have /var/www/html hard coded for path. Debian-based GNU distributions provide easy installation:

sudo bash

//mysql 5.6 provides indexes on derived tables for fast subqueries

apt-get install mysql-server-5.6

apt-get install phpmyadmin (optional)

apt-get install apache2 (included with phpmyadmin)

run the creation .SQL scripts

// username=publicselect password=public

apt-get install php5-cli (for localhosting)

cd /var/www/html

git clone <repository url> .

//test it locally

php -S localhost:8000

//navigate to localhost:8000/frontEnd.html

To scrape org icons, run /dbPop/populate_icons.php. The front end assumes you save to /org_icons, so provide an Apache alias if necessary under 000-default.conf. Icons are stored locally and separately from the database.

To live host, set up port forwarding on your router

//navigate to <your_public_IP>/index.html



# Organization Finder

Website Database for players to find organizations.

The scraper was built from Siegen's sc-api.

To host the app yourself, add and run dependencies. Debian-based GNU+Linux distributions provide easy installation:

``` javascript
sudo bash
```
If using an ARM CPU, you will need to use the stretch repo for MySQL5.6.
```javascript
nano /etc/apt/sources.list
```
add the repo
``` javascript
deb http://archive.raspbian.org/raspbian/ stretch main
```
(this might not work for all project boards)

The remaining instructions hold for all CPUs:
```javascript
apt-get update
```

mysql 5.6 provides indexes on derived tables for fast subqueries
```javascript
apt-get install mysql-server-5.6
```

optional (for easy database viewing)
```javascript
apt-get install phpmyadmin
```

if you did not install phpmyadmin, which includes Apache2
```javascript
apt-get install apache2
```

run the creation .SQL scripts

// username=publicselect password=public

sc-api uses curl
```javascript
apt-get install php5-curl
```

shell script for updates has to resize images
```javascript
apt-get install imagemagick
```

assuming you're hosting using Apache's default folder
```javascript
cd /var/www/html
git clone <repository url> .
```

test it locally
```javascript
apt-get install php5-cli
php -S localhost:8000
```

navigate to localhost:8000/index.html

To scrape org icons, run /dbPop/populate_icons.php. The front end assumes you save to /org_icons, so provide an Apache alias if necessary under 000-default.conf. Icons are stored locally and separately from the database.

To live host, set up port forwarding on your router, and navigate to <your_public_IP>/index.html


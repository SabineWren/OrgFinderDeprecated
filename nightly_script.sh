#!/bin/bash
php5 /var/www/html/dbPop/populate_orgs.php <db_username> <password> &&
php5 /var/www/html/dbPop/delete_orgs.php <db_username> <password> &&
php5 /var/www/html/dbPop/populate_icons.php <db_username> <password> &&
mogrify -path /var/www/html/org_icons -filter Triangle -define filter:support=2 -thumbnail 50 -unsharp 0.25x0.08+8.3+0.045 -dither None -posterize 136 -quality 82 -define jpeg:fancy-upsampling=off -define png:compression-filter=5 -define png:compression-level=9 -define png:compression-strategy=1 -define png:exclude-chunk=all -interlace none -colorspace sRGB /var/www/html/org_icons_new/* &&
mv /var/www/html/org_icons_new/* /var/www/html/org_icons_fullsize/


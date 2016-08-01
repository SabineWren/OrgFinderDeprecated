# Cognition.corp
Cognition Corp website and database development (pre-alpha)

Technology stack:

- Raspian GNU+Linux server (USB stick stores database and pictures)

- MySQL database with utf8-bin tables

- PHP back-end

- AngularJS front-end

- we interface with sc-api

Hosting instructions:

Use PHP5 for local testing and Apache for hosting, as npm does not run php scripts.

To host the app, add and run dependencies. Debian-based GNU distributions keep it simple:

1) git clone <repository url>

//for local testing:

2) php -S localhost:8000

//navigate to localhost:8000/frontEnd.html

//for LAMP server:

3) clone into /var/www/html

//Our test hosting uses Raspian GNU+Linux, Apache2, MySQL-server, and PHP5 administered thru PhpMyAdmin.

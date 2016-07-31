# Cognition.corp
Cognition Corp website and database development (pre-alpha)

Technology stack:

- GNU+Linux server

- MySQL database with utf8-bin tables

- PHP back-end

- AngularJS front-end

- we interface with sc-api

Hosting instrunctions:

Use PHP5 for local testing and Apache for hosting, as npm does not run php scripts.

To host the app locally, add and run dependencies. Debian-based GNU distributions keep it simple:

1) git clone <repository url>

//for local testing:

2) php -S localhost:8000

//navigate to localhost:8000/frontEnd.html

3) clone into /var/www/html (for LAMP server)

//Our test hosting uses Raspian GNU+Linux, Apache2, MySQL-server, and PHP5 administered thru PhpMyAdmin.

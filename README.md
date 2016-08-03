# Cognition.corp
Cognition Corp website and database (live Alpha)

Technology stack (LAMP):

- Raspian GNU+Linux server (USB stick stores database and pictures)

- Apache2

- MySQL-server database (administered thru PhpMyAdmin)

- PHP5 back-end

- AngularJS front-end

- we interface with sc-api

Hosting instructions:

Use PHP5 for local testing and Apache for hosting, as npm does not run php scripts.

To host the app, add and run dependencies. Debian-based GNU distributions keep it simple:

1) git clone <repository url>

//for local testing,

2) php -S localhost:8000

//navigate to localhost:8000/frontEnd.html

//for LAMP server,

3) clone into /var/www/html

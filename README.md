# Cognition.corp
Cognition Corp website and database (live Alpha -- more to come!)

Technology stack (GASP/LAMP):

- GNU+Linux server

- Apache2

- MySQL database

- PHP5 back-end

- AngularJS front-end (deprecated)

- we interface with sc-api to scrape data from RSI

Current hardware:

- 900MHz quad-core ARM Cortex-A7

- 1GiB DDR3

- 8GB USB flash stick (stores database and pictures)

To host the app, add and run dependencies. Debian-based GNU distributions keep it simple:

1) git clone <repository url>

2) install MySQL-Server

3) run the creation .SQL scripts

//test it locally

4) php -S localhost:8000

//navigate to localhost:8000/frontEnd.html

5) for GASP/LAMP server, install Apache2

6) clone into /var/www/html

//naviate to <yourIP>/index.html

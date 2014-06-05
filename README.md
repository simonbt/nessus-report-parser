nessus-report-parser
====================

Parser and outputter for Nessus XML reports

REQUIREMENTS:

apache2
sqlite3
php5-sqlite
php5-curl
curl


CONFIGURATION:

Create Apache2 vhost with Web as the root directory
Edit Web/config.php and ensure that the path is correct to the reportsAPI.php.
If you are using htaccess for authentication you will need to add the username and password into Web/config.php

APACHE2 Vhost Template: (You'll need to add "127.0.0.1  reports.local" into /etc/hosts for this template to work correctly

<VirtualHost *:80>
        ServerName reports.local
        ServerAdmin simon.beattie@randomstorm.com
        DocumentRoot "/Users/simonbeattie/Repos/Git/nessus-report-parser/Web/"

        <Directory "/Users/simonbeattie/Repos/Git/nessus-report-parser/Web/">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>
        ErrorLog "/private/var/log/apache2/reports-vhost.log"
        LogLevel warn
</VirtualHost>


USAGE:

To Import a Report:

Run import.php with the Nessus xml report filename as an argument

        php import.php nessus_report.nessus

To view the output:

Navigate to Web/index.php from a browser.

Updates:

16th April 2014:
    Changed storage engine from MySQL to SQLite3

4th June 2014:
    Added PCI report output


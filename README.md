nessus-report-parser
====================

Parser and outputter for Nessus and OpenDLP XML reports

REQUIREMENTS:

apache2
sqlite3
php5-sqlite

CONFIGURATION:

Create Apache2 vhost with Web as the root directory
If you are using htaccess for authentication you will need to add the username and password into config.php

APACHE2 Vhost Template: (You'll need to add "127.0.0.1  reports.local" into /etc/hosts for this template to work correctly

    <VirtualHost *:80>
            ServerName reports.local
            ServerAdmin simon.beattie@randomstorm.com
            DocumentRoot "/Users/simonbeattie/Repos/Git/nessus-report-parser/"

            <Directory "/Users/simonbeattie/Repos/Git/nessus-report-parser/">
                    Options Indexes FollowSymLinks MultiViews
                    AllowOverride All
                    Order allow,deny
                    allow from all
            </Directory>
            ErrorLog "/private/var/log/apache2/reports-vhost.log"
            LogLevel warn
    </VirtualHost>


INSTALLATION:

Simply run ./install.sh to setup the database and config file.


UPDATING:

Simply run ./update to pull all the latest changes.

Updates:

16th April 2014:
    Changed storage engine from MySQL to SQLite3

4th June 2014:
    Added PCI report output

9th June 2014:

    File Management
        Added the ability to upload reports
            You can currently upload any sort of file
        Added the ability to import reports
            This imports into the database through the interface (exactly the same as if you were to use the import.php script)
        Added the ability to delete reports
            Simply removed the uploaded reports (doesn’t yet remove anything from the database)
        Added the ability to merge report
            This uses a modified version of the python script you all use anyway. I’ve tested merging up to 4 reports at once.
        Interface Updates
            A number of changes to how information is displayed, and generally CSSing

    10th June 2014:
        Limitation to file upload type (.xml & .nessus) -- REMOVED DUE TO SAFARI BUG
        Added 900row limit for vulnerability report tables due to pages bug
        Report output for OpenDLP reports
        Added file management functionality for OpenDLP
        Added OpenDLP reports list

    11th June 2014:
        Complete rewrite of a large portion of the application
        Integrated slim micro framework
        Removed all reliance on Curl
        Nessus report importing fully available through interface

    TO - DO
        Restriction on uploaded files - server-side
        Limitation to file upload sizes
        .xls output for all vulnerabilities
        Authentication
        Template download / storage
        General code tidy up

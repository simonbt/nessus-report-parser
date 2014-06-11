nessus-report-parser
====================

Parser and outputter for Nessus and OpenDLP XML reports

REQUIREMENTS:

    apache2
    sqlite3
    php5-sqlite


INSTALLATION:

    Installation instructions for Mac OSX Mavericks

    Create web directory (change my name for your username):
        mkdir -p /Users/simonbeattie/www
        cd /Users/simonbeattie/www

    Clone the repository:
        git clone https://github.com/simonbt/nessus-report-parser.git

    Setup the system:
        cd nessus-report-parser
        ./install.sh

    Add host line within hosts file:
        sudo nano /etc/hosts
        ADD:

            127.0.0.1  reports.local

    Edit the Apache Configuration:
        sudo nano /private/etc/apache2/httpd.conf
        ADD (top of the file underneath "NameVirtualHost *:80"):

                  <VirtualHost *:80>
                          ServerName reports.local
                          ServerAdmin simon.beattie@randomstorm.com
                          DocumentRoot "/Users/simonbeattie/www/nessus-report-parser/"

                          <Directory "/Users/simonbeattie/www/nessus-report-parser/">
                                  Options Indexes FollowSymLinks MultiViews
                                  AllowOverride All
                                  Order allow,deny
                                  allow from all
                          </Directory>
                          ErrorLog "/private/var/log/apache2/reports-vhost.log"
                          LogLevel warn
                  </VirtualHost>

        REPLACE:

            #LoadModule php5_module libexec/apache2/libphp5.so

        WITH:

            LoadModule php5_module libexec/apache2/libphp5.so

    Restart Apache
        sudo apachectl restart

    Completed:
        You should now be able to navigate to the system: http://reports.local

UPDATING:

    Simply run ./update to pull all the latest changes.

UPDATES:

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

TO-DO:

    Restriction on uploaded files - server-side
    Limitation to file upload sizes
    .xls output for all vulnerabilities
    Authentication
    Template download / storage
    General code tidy up

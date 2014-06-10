#!/bin/bash

echo "Installing..."

if [ -f .installed ]; then
    echo "System already installed, setting permissions!"
    chmod 777 Database
    chmod 777 Library/Uploads/Nessus
    chmod 777 Library/Uploads/OpenDLP
    exit 0
else
    echo "Copying Database"
    cp Database/reports.sqlite.template Database/reports.sqlite
    echo "Copying config"
    cp config.php.template config.php
    echo "Setting permissions"
    chmod 777 Database
    chmod 777 Library/Uploads/Nessus
    chmod 777 Library/Uploads/OpenDLP
    echo  "Complete"
    exit 0
fi
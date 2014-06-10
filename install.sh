#!/bin/bash

echo "Installing..."

if [ -f reports.sqlite ]; then
    echo "System already installed, setting permissions!"
    chmod 777 reports.sqlite
    chmod 777 Web/uploads
fi

echo "Copying Database"
cp reports.sqlite.template reports.sqlite
echo "Copying config"
cp Web/config.php.template Web/config.php
echo "Setting permissions"
    chmod 777 reports.sqlite
    chmod 777 Web/uploads

echo  "Complete"
exit 0


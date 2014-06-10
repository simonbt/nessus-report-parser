#!/bin/bash

echo "Installing..."

if [ -f reports.sqlite ]; then
    echo "System already installed, setting permissions!"
    chmod 777 reports.sqlite
    chmod 777 Web/uploads
    chmod 777 `pwd`
    exit 0
elif [ -f reports.sqlite.template ]; then
    echo "Copying Database"
    cp reports.sqlite.template reports.sqlite
    echo "Copying config"
    cp Web/config.php.template Web/config.php
    echo "Setting permissions"
    chmod 777 reports.sqlite
    chmod 777 Web/uploads
    chmod 777 `pwd`
    echo  "Complete"
    exit 0
else
    echo "ERROR - Please run this from the main directory"
    exit 1
fi
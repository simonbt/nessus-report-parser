#!/bin/bash

echo "Installing..."

if [ -f reports.sqlite ]; then
    echo "System already installed!"
    exit 1

    chmod 777
fi

echo "Copying Database"
cp reports.sqlite.template reports.sqlite
echo "Copying config"
cp Web/config.php.template Web/config.php

echo  "Complete"
exit 0


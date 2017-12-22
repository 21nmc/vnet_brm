#!/bin/sh
snmpwalk -v 2c -c $1 $2 ifDescr > /var/www/html/Traffi/walk.txt


#!/bin/bash
## zabbix登陆地址、zabbix登陆用户
LOGURL="http://211.151.5.8/21m/index.php"
ZABBIX_USER="admins"

ZABBIX_PASS="nmc@ccib"

GRAPHID=1714
##时间段，12H
PERIOD=43200
## 请求地址 保存cookie    
curl -L -c cookie.txt  -d "request=&$ZABBIX_USER&password=$ZABBIX_PASS&autologin=1&enter=Sign+in" $LOGURL
##图片URL地址格式
URL="http://211.151.5.8/21m/chart2.php?graphid=$GRAPHID&period=$PERIOD&width=1222"
##带cookie请求图片URL，并保存
curl -c cookie.txt -b cookie.txt  $URL > /var/www/html/Traffi/graphs/$GRAPHID.png
#http://211.151.5.8/21m/chart2.php?graphid=1714&period=3600&stime=20180717132133&updateProfile=1&profileIdx=web.screens&profileIdx2=1714&sid=584d5678a7212c61&width=1392

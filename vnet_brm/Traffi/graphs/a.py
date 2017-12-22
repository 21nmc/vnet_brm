#!/usr/bin/python
#coding:utf-8
import urllib2,requests,cookielib,urllib
#定义登录地址
login_url = 'http://211.151.5.8/21m/index.php'
#定义登录所需要用的信息，如用户名、密码等，使用urllib进行编码
login_data = urllib.urlencode({
                        "name": 'admins',
                        "password": 'nmc@ccib',
                        "autologin": 1,
                        "enter": "Sign in"})

#设置一个cookie处理器，它负责从服务器下载cookie到本地，并且在发送请求时带上本地的cookie  
cj = cookielib.CookieJar()
opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(cj)) 
urllib2.install_opener(opener)
opener.open(login_url,login_data).read()
#这部分没有具体写
#sql_hostid = select hostid from hosts where host=''; #通过host查询hostid
#sql_itemid = select itemid,`name`,key_ from items where hostid='10251' #通过hostid查询itemid，通过key_过滤性能监视器
sql_graphid = 1714 #通过itemid查询对应的graphid

graph_args = urllib.urlencode({
                        "graphid":'1714',
                        "width":'400',
                        "height":'156',
                        "stime":'20180717140855', #图形开始时间
                        "period":'86400'})


graph_url = 'http://211.151.5.8/21m/chart2.php'
print graph_args  #返回格式：width=400&screenid=28&graphid=4769&period=86400&height=156

data = opener.open(graph_url,graph_args).read()
# print data
file=open('/var/www/html/Traffi/graphs/zaa225.png','wb')
file.write(data)
file.flush()
file.close()

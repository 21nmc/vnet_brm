<?php
$con = mysql_connect("127.0.0.1","root","mysql");
mysql_select_db("boss", $con);
mysql_query("SET NAMES 'UTF8'");

$a = system("bash /var/www/html/Traffi/walk.sh $argv[1] $argv[2]  ");
$lines=file("/var/www/html/Traffi/walk.txt");
foreach ($lines as $value) {
$line=explode("ifDescr.",$value);
$index1= $line[1];
$index = explode(" = ",$index1);
$port = explode("STRING: ",$index1);
$indexport= $index[0];
$portname= trim($port[1]);
//echo $indexport." ".$portname;
mysql_query("insert into vnet_traffic_portlist2 (portindexid,portname,host,hostid) values ('$indexport','$portname','$argv[2]','$argv[3]')");

}
?>


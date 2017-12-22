<?php
error_reporting(0);
require_once 'include/ZabbixApiAbstract.class.php';
//use ZabbixApi\ZabbixApi;
require_once 'include/ZabbixApi.class.php';
require_once 'include/superpageclass.php';
date_default_timezone_set('Asia/Shanghai');
$t=time();
$t_1=time()-90000;
$con = mysql_connect("20.58.8.28:13306","root","Mjkpt$4962.");
mysql_select_db("smnpc", $con);
mysql_query("SET NAMES 'UTF8'");
$api = new ZabbixApi('http://20.58.8.30/zabbix/api_jsonrpc.php', "api", "Zapi$4962.");

//$triggerid_get = $api->itemGet($params=array("output"=>array("extend"),"search"=>array("key_"=>""),"monitored"=>1,"sortorder"=>"DESC","skipDependent"=> 1));


$itemid_get = $api->itemGet($params=array("output"=>array("extend"),"hostids"=>10084,"search"=>array("key_"=>"system")));

//var_dump($itemid_get);
foreach ($itemid_get as $key ) {
    $hostid=$key->hostid;
    $item_id=$key->item_id;
    $age=$t-$lastchange;
    $age_t= gmstrftime('%H 小时 %M 分钟 %S 秒 ',$age);

    $delsql="delete from vnet_temp_items where hostid='".$hostid."' and item_id='".$item_id."'  ";
    mysql_query($delsql,$con);
    $sql="insert into vnet_temp_items (hostid,item_id) values (".$hostid.",".$item_id.")";
    mysql_query($sql,$con);

}
mysql_close($con);
?>


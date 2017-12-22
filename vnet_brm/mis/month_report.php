<?php 
header("Content-type: text/html; charset=utf-8");
//加载公共函数
require_once('functions.php');
$err['msg'] = 'no';
$succ['msg'] = 'ok';

$dbconn = mysql_connect("211.151.5.12","root","mysql");
mysql_select_db("boss", $dbconn) or die("Data Link Error!");
mysql_query("SET NAMES 'UTF8'");

$md5   =  md5("21vianet");
$postmd5   =  trim($_REQUEST['md5']);
if ($md5==$postmd5){

    $sql='select roomcode,productcode,bandwidth_month,year,month from vnet_mis_month   ';
	$result=mysql_query($sql,$dbconn);
	$temparray=array();
	$n=0;
	while ($row=mysql_fetch_array($result)) {
	    $roomcode=$row[0];
	    $productcode=$row[1];
	    $bandwidth_month=$row[2];
	    $year=$row[3];
	    $month=$row[4];
        $temparray[$n]['roomcode']=$row[0];
        
        $n++;
	}
    msg_return($succ);
    	     $strs ='{"data":'.$str.',"PageCount":"'.$PageCount.'","rsCount":"'.$rsCount.'","state":"'.$state.'"}';

     echo urldecode($strs);
}else{
	msg_return($err);
}


mysql_close($dbconn);

?>

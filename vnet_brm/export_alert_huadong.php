<?php

header("Content-type:text/html;charset=utf-8");

function getExcel($fileName,$headArr,$data){
    //对数据进行检验
    if(empty($data) || !is_array($data)){
        die("data must be a array");
    }
    //检查文件名
    if(empty($fileName)){
        exit;
    }

    $date = date("Y_m_d",time());
    $fileName .= "_{$date}.xls";

    //创建PHPExcel对象，注意，不能少了\
    $objPHPExcel = new PHPExcel();
    $objProps = $objPHPExcel->getProperties();
     
    //设置表头
    $key = ord("A");
    foreach($headArr as $v){
        $colum = chr($key);
        $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
        $key += 1;
    }

    $column = 2;
    $objActSheet = $objPHPExcel->getActiveSheet();
    foreach($data as $key => $rows){ //行写入
        $span = ord("A");
        foreach($rows as $keyName=>$value){// 列写入
            $j = chr($span);
            $objActSheet->setCellValue($j.$column, $value);
            $span++;
        }
        $column++;
    }

    $fileName = iconv("utf-8", "gb2312", $fileName);
    //重命名表
    // $objPHPExcel->getActiveSheet()->setTitle('test');
    //设置活动单指数到第一个表,所以Excel打开这是第一个表
    $objPHPExcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=\"$fileName\"");
    header('Cache-Control: max-age=0');

    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output'); //文件通过浏览器下载
    exit;
}


//$headArr=array("客户名称","网络设备名称","端口描述","带宽");
$headArr=array("网络设备","告警端口","告警端口描述","端口in流量","端口out流量","阀值","端口所属公司","告警描述");

require './Public/excel/PHPExcel.class.php';
require './Public/excel/PHPExcel/Writer/Excel5.php';
require './Public/excel/PHPExcel/IOFactory.php';

$dbconn = mysql_connect("127.0.0.1","root","mysql");
mysql_select_db("boss", $dbconn) or die("Data Link Error!");
mysql_query("SET NAMES 'UTF8'");


$sql_one = "select max(version) as version from vnet_cust_alert";
$_results =mysql_query($sql_one,$dbconn);
$versions =  mysql_fetch_row($_results);
$version = intval($versions[0]);


$sql="select * from vnet_cust_alert where version = $version and depart_id = 4";
$_result =mysql_query($sql,$dbconn);

$all = array();
while($row = mysql_fetch_row($_result)){
    $temp = array();
    $temp['name'] = $row[1];
    $temp['port'] = $row[2];
    $temp['desc'] = $row[3];
    $temp['in'] = $row[4];
    $temp['out'] = $row[5];
    $temp['fa'] = $row[11];
    $temp['gongsi'] = $row[9];
    $temp['miaoshu'] = $row[7];
    $all[] = $temp;
}

$filename="alert_data_huadong";
getExcel($filename,$headArr,$all);

?>
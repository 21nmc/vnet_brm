<?php

header("Content-type:text/html;charset=utf-8");

function getallU($ps,$length){
    $temp = array();
    $ss = $ps;
    for($i=0;$i<$length;$i++){
        
        
        $temp[] = $ss;
        $ss+=1;
    }
    return implode(',', $temp);
} 

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


require './Public/excel/PHPExcel.class.php';
require './Public/excel/PHPExcel/Writer/Excel5.php';
require './Public/excel/PHPExcel/IOFactory.php';

$dbconn = mysql_connect("127.0.0.1","root","");
mysql_select_db("boss", $dbconn) or die("Data Link Error!");
mysql_query("SET NAMES 'UTF8'");

$sql_brand='select * from vnet_agents';
$result_brand =mysql_query($sql_brand,$dbconn);
$all_brand= array();
while($row = mysql_fetch_row($result_brand)){
    $all_brand[$row[0]] = $row[2];
}

$sql_depart='select * from vnet_department';
$result_depart =mysql_query($sql_depart,$dbconn);
$all_depart = array();
while($row = mysql_fetch_row($result_depart)){
    $all_depart[$row[0]] = $row[1];
}

$sql_racks = "select id,name from vnet_ipdb_racks";
$result_racks = mysql_query($sql_racks,$dbconn);
$all_racks = array();
while($row = mysql_fetch_row($result_racks)){
    $all_racks[$row[0]] = $row[1];
}

$sql_locations = "select id,name from vnet_ipdb_locations";
$result_locations = mysql_query($sql_locations,$dbconn);
$all_locations = array();
while($row = mysql_fetch_row($result_locations)){
    $all_locations[$row[0]] = $row[1];
}


$sql_locareas = "select id,areaname from vnet_ipdb_locareas";
$result_locareas = mysql_query($sql_locareas,$dbconn);
$all_locareas = array();
while($row = mysql_fetch_row($result_locareas)){
    $all_locareas[$row[0]] = $row[1];
}

$sql_item_type = "select id,typedesc from vnet_itemtype";
$result_type = mysql_query($sql_item_type,$dbconn);
$all_type = array();
while($row = mysql_fetch_row($result_type)){
    $all_type[$row[0]] = $row[1];
}

$sql_ = "select id,typedesc from vnet_itemtype";
$result_type = mysql_query($sql_item_type,$dbconn);
$all_type = array();
while($row = mysql_fetch_row($result_type)){
    $all_type[$row[0]] = $row[1];
}


$depart_id = $_REQUEST['depart_id'];
if ($depart_id == "-1") {
    $sql_items = "select * from vnet_ipdb_items";
}else{
    $sql_items = "select * from vnet_ipdb_items where depart_id = $depart_id";
}
$result_items = mysql_query($sql_items,$dbconn);

$all_datas = array();
while($row = mysql_fetch_row($result_items)){
    $temp = array();
    $temp['common_name'] =  $row[5];
    $temp['itemtypeid'] = $all_type[$row[1]];
    $temp['department'] = $all_depart[$row[42]];
    if (empty($row[20])) {
        $temp['locationid'] = "";
    }else{
        $temp['locationid'] = $all_locations[$row[20]]."/".$all_locareas[$row[40]]."/".$all_racks[$row[32]];
    }
    $postiosn = $row[33];
    $usize = $row[24];
    if ($postiosn!= "" && $usize != "") {
        $temp['u'] = getallU($postiosn, $usize);
    }else{
        $temp['u'] = "";
    }
    $temp['brand'] = $all_brand[$row[44]];
    $temp['xinghao'] = $row[4];
    $temp['sn'] = $row[6];
    
    $all_datas[]= $temp;
}


$headArr=array("设备名称","设备类型","所属部门","设备位置","所占U位","品牌","型号","sn号");
$filename="device_data";
getExcel($filename,$headArr,$all_datas);

?>
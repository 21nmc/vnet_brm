<?php
error_reporting(0);
set_time_limit(0);
// $con = mysql_connect("127.0.0.1","root","");
// mysql_select_db("test", $con);
// mysql_query("SET NAMES 'UTF8'");

// $dbconn_76 = mysql_connect("211.151.5.12","root","mysql");
// mysql_select_db("testrack76",$dbconn_76) or die("Data Link Error!");
// mysql_query("SET NAMES 'UTF8'");

$con = mysql_connect("127.0.0.1","root","mysql");
mysql_select_db("zichan",$con) or die("Data Link Error1!");
mysql_query("SET NAMES 'UTF8'");

$dbconn_76 = mysql_connect("211.151.2.76","root","mysql");
mysql_select_db("rack",$dbconn_76) or die("Data Link Error2!");
mysql_query("SET NAMES 'UTF8'");




$start_time=microtime(true); //获取程序开始执行的时间
echo "执行中请勿重复操作！！！<br />";

//初始化----------需要判断是否数据同步的动态表有 vnet_agents vnet_department vnet_itemtype vnet_product 


//select * from dictionary where chapter_id=10005  所属产品 vnet_product
//select * from dictionary where  chapter_id=10004  设备品牌 vnet_agents
//select * from dictionary where chapter_id=1  设备类型 vnet_itemtype
//select * from dictionary where chapter_id=10000  部门 vnet_department
//从76表查询存在更新名称，不存在12服务器插入



$sql_pro = "select * from dictionary where chapter_id=10005 ";  //产品
$result_pro = mysql_query($sql_pro,$dbconn_76);
while ($row_pro = mysql_fetch_array($result_pro))
{
  $productname = $row_pro['dict_value'];
  $checkprd_sql = "select id from vnet_product where title ='".$productname."'";
  $result_prd=mysql_query($checkprd_sql,$con);
  if ($rowpidcheck = mysql_fetch_array($result_prd)){$check_prdid=$rowpidcheck['id'];}
  if (empty($check_prdid)){
      $sql_prd="insert into vnet_product (title)values('".$productname."')";
      mysql_query($sql_prd,$con);
  }
}


$sql_itemtype = "select * from dictionary where chapter_id=1 ";  //设备类型
$result_itemtype = mysql_query($sql_itemtype,$dbconn_76);
while ($row_itemtype = mysql_fetch_array($result_itemtype))
{
  $itemtypename = $row_itemtype['dict_value'];
  $checkitemtype_sql = "select id from vnet_itemtype where typedesc ='".$itemtypename."'";
  $result_itemtype2=mysql_query($checkitemtype_sql,$con);
  if ($rowitemtypecheck = mysql_fetch_array($result_itemtype2)){$check_itemtypeid=$rowitemtypecheck['id'];}
  if (empty($check_itemtypeid)){
      $sql2_itemtype="insert into vnet_itemtype (typedesc)values('".$itemtypename."')";
      mysql_query($sql2_itemtype,$con);
  }
}


$sql_agent = "select * from dictionary where chapter_id=10004 ";  //品牌
$result_agent = mysql_query($sql_agent,$dbconn_76);
while ($row_agent = mysql_fetch_array($result_agent))
{
  $agentname = $row_agent['dict_value'];
  $checkagent_sql = "select id from vnet_agents where title ='".$agentname."'";
  $result_agent2=mysql_query($checkagent_sql,$con);
  if ($rowagentcheck = mysql_fetch_array($result_agent2)){$check_agentid=$rowagentcheck['id'];}
  if (empty($check_agentid)){
      $sql2_agent="insert into vnet_agents (title)values('".$agentname."')";
      mysql_query($sql2_agent,$con);
  }
}



$sql_depart = "select * from dictionary where chapter_id=10000 ";  //部门
$result_depart = mysql_query($sql_depart,$dbconn_76);
while ($row_depart = mysql_fetch_array($result_depart))
{
  $depart_name = $row_depart['dict_value'];
  $checkdepart_sql = "select id from vnet_department where depart_name ='".$depart_name."'";
  $result2_depart=mysql_query($checkdepart_sql,$con);
  if ($rowdepartcheck = mysql_fetch_array($result2_depart)){$check_departid=$rowdepartcheck['id'];}
  if (empty($check_departid)){
      $sql2_depart="insert into vnet_department (depart_name)values('".$depart_name."')";
      mysql_query($sql2_depart,$con);
  }
}









//---------------第一步 同步rack表的所有不重复机柜名称数据  ok-----

$sql = "select * from object where objtype_id=1560 group by name"; //先删除再插入
$result = mysql_query($sql,$dbconn_76);
while ($row = mysql_fetch_array($result))
{
  $name =  $row['name'];
  $label =  $row['label'];
  $comments =  $row['comments'];

  $checksql = "select id from vnet_ipdb_racks where name='".$name."' ";
  $checkresult = mysql_query($checksql,$con);
  if ($rowcheck = mysql_fetch_array($checkresult)){$checkid=$rowcheck['id'];}
  if (empty($checkid)){
      $sql2="insert into vnet_ipdb_racks (name,comments,label)values('".$name."','".$comments."','".$label."')";
  }else{
      $sql2="update vnet_ipdb_racks set comments='".$comments."',label='".$label."' where name='".$name."'";
  }

  //echo $sql2."=====<br />";
  mysql_query($sql2,$con);

}


//---------------第二步 同步rack表的机柜U大小   ok--------
$sql = "select attr.object_id,attr.uint_value,o.name from object as o,attributevalue as attr where o.objtype_id=1560 and attr.object_id=o.id group by o.name";
$result = mysql_query($sql,$dbconn_76);
while ($row = mysql_fetch_array($result))
{
  $uint_value =  $row['uint_value'];
  $name =  $row['name'];
  $object_id =  $row['object_id'];

  $sql2="update vnet_ipdb_racks set usize='".$uint_value."',nodeid='34' where name='".$name."'";

  //echo $sql2."<br />";
  mysql_query($sql2,$con);

}


//---------------第三步 同步rack表的部门id ok --------
$sql ="select dict_key,dict_value from dictionary where chapter_id=10000";
$result = mysql_query($sql,$dbconn_76);
while ($row = mysql_fetch_array($result))
{
    $dict_key = $row['dict_key'];
    $depart_name = $row['dict_value'];

    $sql2="select object_id from attributevalue where uint_value=".$dict_key." and object_tid=1560";
    //echo $sql2."<br />";
    $result2=mysql_query($sql2,$dbconn_76);
    while ($row2 = mysql_fetch_array($result2))
    {
        $object_id = $row2['object_id'];
        $sql3="select name from object where id=".$object_id;
        //echo $sql3."<br />";
        $result3 = mysql_query($sql3,$dbconn_76);
        if ($rowcheck = mysql_fetch_array($result3)){
            $rack_name=$rowcheck['name'];

            $sql4="select id from vnet_department where depart_name='".$depart_name."'";
            //echo $sql4."----<br />";
            $result4 = mysql_query($sql4,$con); 
            //获取vnet_department的id
            if ($row_getdapid = mysql_fetch_array($result4)){$dapid = $row_getdapid['id'];}
            $sql5="update vnet_ipdb_racks set departmentid='".$dapid."' where name='".$rack_name."'";
            mysql_query($sql5,$con);
            
        }
         
    }


}





//---------------第四步 同步所有机房 需要排重操作否则数据错误  ok------
$sql = "select name from object where objtype_id=1562 and id not in (44,58,59,60,1296,1242) group by name";
$result = mysql_query($sql,$dbconn_76);
while ($row = mysql_fetch_array($result))
{
  $name =  $row['name'];

  $checksql = "select id from vnet_ipdb_locations where name='".$name."' ";
  $checkresult = mysql_query($checksql,$con);
  if ($rowcheck = mysql_fetch_array($checkresult)){
      $checkid2=$rowcheck['id'];
  }  

  if (empty($checkid2)){
      $sql2="insert into vnet_ipdb_locations (name,nodeid)values('".$name."','16')";
  }else{
      $sql2="update vnet_ipdb_locations set nodeid='16' where name='".$name."'"; //不需要更新操作 检查脚本用
  } 
  //echo $sql2."<br />";
  mysql_query($sql2,$con);
}





//---------------第五步 同步所有房间 需要排重操作否则数据错误  ok------
$sql = "select name from object where objtype_id=1561 group by name";
$result = mysql_query($sql,$dbconn_76);
while ($row = mysql_fetch_array($result))
{
  $name =  $row['name'];

  $checksql = "select id from vnet_ipdb_locareas where areaname='".$name."' ";
  $checkresult = mysql_query($checksql,$con);
  if ($rowcheck = mysql_fetch_array($checkresult)){$checkid3=$rowcheck['id'];}  
  if (empty($checkid3)){
      $sql2="insert into vnet_ipdb_locareas (areaname,nodeid)values('".$name."','17')";
  }else{
      $sql2="update vnet_ipdb_locareas set nodeid='17' where areaname='".$name."'"; //不需要更新操作 检查脚本用
  } 
  //echo $sql2."<br />";
  mysql_query($sql2,$con);

}




//---------------第六步 同步所有房间关联的机房ID 和所有机柜对应的房间和机房id 和机房的区域 ok------

$area='';
$allsql = "select id from object where objtype_id=1562 and id  in(44,58,59,60,1296,1242)"; //44=华北区 58=华南区 59=华东区 60 华西区 华中区=1296 西南区=1242
$allresult = mysql_query($allsql,$dbconn_76);
while ($allrow = mysql_fetch_array($allresult))
{
    $qid =  $allrow['id'];
    
    if ($qid==44){$area='华北区';}elseif ($qid==58){$area='华南区';}elseif ($qid==59){$area='华东区';}elseif ($qid==60){$area='华西区';}elseif ($qid==1296){$area='华中区';}elseif ($qid==1242){$area='西南区';}else{$area='';}

    $sql = "select child_entity_id from entitylink where parent_entity_id=".$qid; //44=华北区 58=华南区 59=华东区 60 华西区 华中区=1296 西南区=1242
    $result = mysql_query($sql,$dbconn_76);
    $all_roomid="";
    while ($row = mysql_fetch_array($result))
    {
      $false_room_id =  $row['child_entity_id'];
      //获取机房信息
      $sql2 = "select name from object where id=".$false_room_id;
      $result2 = mysql_query($sql2,$dbconn_76);
      if ($row2 = mysql_fetch_array($result2)){
          $location_name =  $row2['name'];
          //获取房间里边的机柜信息
          $sql3 = "select child_entity_id from entitylink where parent_entity_id=".$false_room_id;
          //echo $sql3."<br />";
          $result3 = mysql_query($sql3,$dbconn_76);
          while ($row3 = mysql_fetch_array($result3)){
              $true_area_id =  $row3['child_entity_id'];//房间id
              $sql4 = "select name from object where id=".$true_area_id;

              $result4 = mysql_query($sql4,$dbconn_76);
              if ($row4 = mysql_fetch_array($result4)){
                  $locarea_name =  $row4['name'];
              }
              //获取机房id 
              $sql5 = "select id from vnet_ipdb_locations where name='".$location_name."' ";
              $result5 = mysql_query($sql5,$con);
              if ($row5 = mysql_fetch_array($result5)){
                  $true_location_id =  $row5['id'];
              }
              //获取房间id
              $sql6 = "select id from vnet_ipdb_locareas where areaname='".$locarea_name."' ";
              //echo $sql6."<br />";
              $result6 = mysql_query($sql6,$con);
              if ($row6 = mysql_fetch_array($result6)){
                  $true_locarea_id =  $row6['id'];
              }
              //更新房间表的机房和房间关系id
              $sql7="update vnet_ipdb_locareas set locationid='".$true_location_id."'  where areaname='".$locarea_name."'";
              //echo $sql7."<br />";
              $result7 = mysql_query($sql7,$con);

              $sql11="update vnet_ipdb_locations set area='".$area."'  where id='".$true_location_id."'";
              //echo $sql11."<br />";
              $result11 = mysql_query($sql11,$con);



            $sql10 = "select child_entity_id from entitylink where parent_entity_id=".$true_area_id;
            //echo $sql10."<br />";
            $result10 = mysql_query($sql10,$dbconn_76);
            while ($row10 = mysql_fetch_array($result10)){
                $true_rack_id =  $row10['child_entity_id'];//机柜id

                  $sql9="select name from object where id=".$true_rack_id;
                  
                  $result9 = mysql_query($sql9,$dbconn_76);
                  if ($row9 = mysql_fetch_array($result9)){
                      $true_rack_name =  $row9['name'];
                  }
                  //echo $true_rack_name."<br />";
                  //更新机柜表的机房和房间关系id
                  $sql8="update vnet_ipdb_racks set locationid='".$true_location_id."',locareaid='".$true_locarea_id."',nodeid='34' where name='".$true_rack_name."'";
                  $result8 = mysql_query($sql8,$con);
                  //echo $sql8."<br />";
                  //echo $true_location_id."----".$true_area_id."<br />";

              }

          }

      }





    }


}






//---------------第七步 name不能为空！判重后插入资产表数据 ok------

$sql = "select * from object where objtype_id not in(1560,1561,1562)  and name!='' group by name"; 
$result = mysql_query($sql,$dbconn_76);
while ($row = mysql_fetch_array($result))
{
  $name =  $row['name'];
  $label =  $row['label'];
  $sn =  $row['asset_no'];
  $comment =  $row['comment'];

  $checksql = "select id from vnet_ipdb_items where common_name='".$name."' ";
  $checkresult = mysql_query($checksql,$con);
  if ($rowcheck = mysql_fetch_array($checkresult)){$checkid4=$rowcheck['id'];}
  if (empty($checkid4)){
      $sql2="insert into vnet_ipdb_items (common_name,comments,label,sn)values('".$name."','".$comments."','".$label."','".$sn."')";
  }else{
      $sql2="update vnet_ipdb_items set comments='".$comments."',label='".$label."',sn='".$sn."' where common_name='".$name."'";
  }

  //echo $sql2."=====<br />";
  mysql_query($sql2,$con);

}





//---------------第八步 对76已经划分部门资产!! 针对12的资产表进行资产部门数据同步 ok------

$sqls = "select dict_key from dictionary where chapter_id=10000";//部门集合 76表
$results = mysql_query($sqls,$dbconn_76);
$ids='';
while ($rows = mysql_fetch_array($results))
{
    $dict_key =  $rows['dict_key'];
    $ids=$ids.','.$dict_key;
}
$ids=substr($ids,1,strlen($ids));

$sql = "select object_id,uint_value from attributevalue where uint_value in(".$ids.") and object_tid not in(1560, 1561, 1562)";
$result = mysql_query($sql,$dbconn_76);
while ($row = mysql_fetch_array($result))
{
  $object_id =  $row['object_id'];
  $uint_value =  $row['uint_value']; //部门id =76表
  $sql_old = "select dict_value from dictionary where dict_key=".$uint_value;
  $result_old = mysql_query($sql_old,$dbconn_76);
  if ($row_old = mysql_fetch_array($result_old)){$old_depatment_name=$row_old['dict_value'];}
  $sql4="select id from vnet_department where depart_name='".$old_depatment_name."'";
  $result4 = mysql_query($sql4,$con);
  if ($row4 = mysql_fetch_array($result4)){$new_depatment_id=$row4['id'];} //获取12新服务器部门id



  $sql2 = "select name from object where id='".$object_id."' ";
  $result2 = mysql_query($sql2,$dbconn_76);
  if ($row2 = mysql_fetch_array($result2)){$items_name=$row2['name'];}

  $checksql = "select * from vnet_ipdb_items where common_name='".$items_name."' ";
  $checkresult = mysql_query($checksql,$con);
  if ($rowcheck = mysql_fetch_array($checkresult)){ //对76已经划分部门资产!!
    $checkid5=$rowcheck['id'];
    $common_name=$rowcheck['common_name'];
    $label=$rowcheck['label'];
    $comments=$rowcheck['comments'];
    $sn=$rowcheck['asset_no'];
    if (empty($checkid5)){
        $sql3="insert into vnet_ipdb_items (common_name,comments,label,sn,depart_id)values('".$common_name."','".$comments."','".$label."','".$sn."','".$new_depatment_id."')";
    }else{
        $sql3="update vnet_ipdb_items set comments='".$comments."',label='".$label."',sn='".$sn."',depart_id='".$new_depatment_id."' where common_name='".$common_name."' ";
    }    
  }


  //echo $sql3."=====<br />";
  mysql_query($sql3,$con);

}




//---------------第九步 同步新服务器-vnet_ipdb_items 关键信息！！！ ok------
$sql="select id,name from object where objtype_id not in(1560,1561,1562) and name!='' group by name"; //获取所有资产id
$result = mysql_query($sql,$dbconn_76);
while ($row = mysql_fetch_array($result))
{
  $obj_id =  $row['id'];
  $common_name =  $row['name'];
  $sql2="select unit_no,rack_id,atom from rackspace where object_id=".$obj_id." group by unit_no"; //获取U大小设备的
  //echo $sql2."---<br >";
  $result2 = mysql_query($sql2,$dbconn_76);
  $i=0;
  while ($row2 = mysql_fetch_row($result2)){
    $temprackposition=$row2[0];
    $rack_id=$row2[1];
    $atom=$row2[2];
    $i++;
  }
  $usize=$i;
  $rackposition=($temprackposition-$usize)+1;
//从 atom表获取 molecule_id 

//-----------------------------------------------------------获取机柜U深度
  $sql4="select atom from rackspace where object_id=".$obj_id." group by atom";
  $result4 = mysql_query($sql4,$dbconn_76);
  $atoms='';
  while ($row4 = mysql_fetch_array($result4)){
    $atom=$row4['atom'];
    $atoms=$atoms.','.$atom;
  }
  $atoms=substr($atoms,1,strlen($atoms));
  $atoms_array=explode(',',$atoms);


  if (in_array("front",$atoms_array) && in_array("interior",$atoms_array) && in_array("rear",$atoms_array)){
      $rackposdepth=6;
  }elseif (in_array("front",$atoms_array) && in_array("interior",$atoms_array)){
      $rackposdepth=5;
  }elseif (in_array("front",$atoms_array)){
      $rackposdepth=4;
  }elseif (in_array("interior",$atoms_array) && in_array("rear",$atoms_array)){
      $rackposdepth=3;
  }elseif (in_array("interior",$atoms_array)){
      $rackposdepth=2;
  }elseif (in_array("rear",$atoms_array)){
      $rackposdepth=1;
  }else{
      $rackposdepth='';
  }
//--------------------------------------------------------------------------------------


  $sql3="select locareaid,locationid from vnet_ipdb_racks where id =".$rack_id;
  $result3 = mysql_query($sql3,$con);
  if ($row3 = mysql_fetch_array($result3))
  {
    $locareaid =  $row3['locareaid'];
    $locationid =  $row3['locationid'];
  }

  $sql6="select string_value from attributevalue where object_id=".$obj_id." and  attr_id=10020"; //10020=设备型号:  WS-C3750-48TS
  $result6 = mysql_query($sql6,$dbconn_76);
  if ($row6 = mysql_fetch_array($result6))
  {
    $model =  $row6['string_value'];
  }  


  $sql7="select uint_value from attributevalue where object_id=".$obj_id." and  attr_id=10017"; //10017=所属产品:  全线BGP
  $result7 = mysql_query($sql7,$dbconn_76);
  if ($row7 = mysql_fetch_array($result7))
  {
    $uint_value =  $row7['uint_value'];
  }  
  $sql8="select dict_value from dictionary where dict_key=".$uint_value; //10017=所属产品:  全线BGP
  $result8 = mysql_query($sql8,$dbconn_76);
  if ($row8 = mysql_fetch_array($result8))
  {
      $belong_product =  $row8['dict_value'];
  }   
 
  //等待杨坤做成产品类型动态获取 权限bgp

  $sql9="select * from vnet_product where title ='".$belong_product."'";
  $result9 = mysql_query($sql9,$con);
  if ($row9 = mysql_fetch_array($result9))
  {
     $product_id =  $row9['id'];
  }


 
  $sql10="select uint_value from attributevalue where object_id=".$obj_id." and  attr_id=10018"; //10018=设备品牌 思科
  //echo $sql10."---<br >"; 
  $result10 = mysql_query($sql10,$dbconn_76);
  if ($row10 = mysql_fetch_array($result10))
  {
    $uint_value10 =  $row10['uint_value'];
  }  
  //select dict_value from dictionary where dict_key=50037
  $sql11="select dict_value from dictionary where dict_key=".$uint_value10; //10018=设备品牌 思科
  $result11 = mysql_query($sql11,$dbconn_76);
  if ($row11 = mysql_fetch_array($result11))
  {
      $dict_value11 =  $row11['dict_value'];
  } 

  $sql14="select id from vnet_agents where title='".$dict_value11."'"; //10018=设备品牌 H3C=H3C
  $result14 = mysql_query($sql14,$con);
  if ($row14 = mysql_fetch_array($result14))
  {
      $manufacturerid =  $row14['id'];
  } 



  $sql15="select objtype_id from object where id=".$obj_id." "; //1=设备类型  Router
  //echo $sql15."---<br >"; 
  $result15 = mysql_query($sql15,$dbconn_76);
  if ($row15 = mysql_fetch_array($result15))
  {
    $uint_value15 =  $row15['objtype_id'];
  }

  $sql12="select dict_value from dictionary where chapter_id=1 and dict_key=".$uint_value15; // chapter_id=1  所有设备类型 dictionary表
  $result12 = mysql_query($sql12,$dbconn_76);
  if ($row12 = mysql_fetch_array($result12))
  {
    $dict_value12 =  $row12['dict_value'];
  }  

  $sql13="select * from vnet_itemtype where typedesc='".$dict_value12."'"; // chapter_id=1  所有设备类型 dictionary表
  $result13 = mysql_query($sql13,$con);
  if ($row13 = mysql_fetch_array($result13))
  {
    $itemtypeid =  $row13['id'];
  } 

  $sql5="update vnet_ipdb_items set itemtypeid='".$itemtypeid."',manufacturerid='".$manufacturerid."',belong_product='".$product_id."',model='".$model."',rackposdepth='".$rackposdepth."',usize='".$usize."',locationid='".$locationid."',locareaid='".$locareaid."',rackid='".$rack_id."',rackposition='".$rackposition."' where common_name='".$common_name."' ";
  //echo $sql5."---<br >";
  mysql_query($sql5,$con);


}

















 $end_time=microtime(true);//获取程序执行结束的时间
 $total=$end_time-$start_time; //计算差值
 echo "此php文件中代码执行了{$total}秒";




?>ok
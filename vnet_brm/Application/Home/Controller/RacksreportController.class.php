<?php
namespace  Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class RacksreportController extends HomeController{


//by zwj 时间戳转换天
function wordTime($time) {
    $time = (int) substr($time, 0, 10);
    $int = time() - $time;
    $str = '';
    if ($int <= 2){
        $str = sprintf('刚刚', $int);
    }elseif ($int < 60){
        $str = sprintf('%d秒前', $int);
    }elseif ($int < 3600){
        $str = sprintf('%d分钟前', floor($int / 60));
    }elseif ($int < 86400){
        $str = sprintf('%d小时前', floor($int / 3600));
    }elseif ($int < 2592000){
        $str = sprintf('%d天前', floor($int / 86400));
    }else{
        $str = date('Y-m-d H:i:s', $time);
    }
    return $str;
}



public function  index(){

   if(!is_login()){
     $this->redirect("User/login");
   }    

  $uid        =   is_login();
  $nickname = M('Member')->getFieldByUid($uid, 'nickname');

  $Ipdb_locations =M("ipdb_locations");
  $Ipdb_locareas =M("ipdb_locareas");
  $Ipdb_racks =M("ipdb_racks");
  $Ipdb_items =M("ipdb_items");

  
  $areaid= $_REQUEST['areaid'];
  $locid= $_REQUEST['locid'];
  $flag= $_REQUEST['flag'];
  $rackid= $_REQUEST['rackid'];
  $location_id= $_REQUEST['location_id'];
  $lids=implode(',',$location_id);
  //-----------------------------获取动态机房，房间数据
  $showlabel='';
  $locationlist=$Ipdb_locations->group(' area ')->order('id asc')->select();
  $rand=10000;
  for($i=0;$i<count($locationlist);$i++){
      $lid=$locationlist[$i]['id'];
      $distname=$locationlist[$i]['area'];
      if (in_array($lid, $location_id)){
         $checked='checked';  
      }else{
        if (empty($lids)){
           $checked='checked';
        }else{
           $checked='';
        }
      }

      $showlabel.='<div class="tagbox" style="text-align:left; padding-left:0px;"><label>';
      $showlabel.='<input type="checkbox" name="location_id[]" class="0" value="'.$rand.'" '.$checked.' onclick="checkAll(this)">';
      $showlabel.=$distname;
      $showlabel.='</label>';//<a  href="" "#"=""> - </a>
      $showlabel.='<div >';

      $sublocationlist=$Ipdb_locations->where(' area="'.$distname.'" ')->order('id asc')->select();
      for($ii=0;$ii<count($sublocationlist);$ii++){
        $sublid=$sublocationlist[$ii]['id'];
        $name=$sublocationlist[$ii]['name'];

        if (in_array($sublid, $location_id)){
           $checked='checked';  
        }else{
          if (empty($lids)){
             $checked='checked';
          }else{
             $checked='';
          }
        }

        $showlabel.='<div class="tagbox" style="text-align:left; padding-left:16px;">';
        $showlabel.='<label><input type="checkbox" name="location_id[]" class="1" value="'.$sublid.'" '.$checked.' onclick="checkAll(this)" >';
        $showlabel.=$name;
        $showlabel.='</label></div>';
      }      

      $showlabel.='</div></div>';

    $rand++;

  }
/*
区域  机柜数 设备数 使用率
华北  453 492 8.06%

机房  机柜数 设备数 使用率
北京B28数据中心 125 124 6.86%

*/

  if(empty($lids)){
      $list = $Ipdb_locations->order('id desc')->select();
  }else{
      $list = $Ipdb_locations->where('id in ('.$lids.') ')->order('id desc')->select();
  }



  for($i=0;$i<count($list);$i++){

    $id=$list[$i]['id'];
    $name=$list[$i]['name'];
    $floor=$list[$i]['floor'];

    $list[$i]['name']='<a href="'.__ROOT__.'/index.php/Home/Racksreport/index/flag/area/locid/'.$id.'.html" >»'.$name.'</a>';


    $rackscount = $Ipdb_racks->where('locationid ="'.$id.'" ')->order('id desc')->count();
    $list[$i]['rackscount']=$rackscount;


    $itemscount = $Ipdb_items->where('locationid ="'.$id.'" ')->order('id desc')->count();
    $list[$i]['itemscount']=$itemscount;

    $rackslistcount = $Ipdb_racks->where('locationid ="'.$id.'" ')->field(' sum(usize) as rack_usizecount')->select();
    $rack_usizecount=$rackslistcount[0]['rack_usizecount'];

    $itemslist = $Ipdb_items->where('locationid ="'.$id.'" ')->field(' sum(usize) as item_usizecount')->select();
    $item_usizecount=$itemslist[0]['item_usizecount'];
    $busy=($item_usizecount/$rack_usizecount)*100;
    $busy=round($busy,2);
    $list[$i]['busy']=$busy.'%';

    
  }


if ($flag=='area'){
    $list = $Ipdb_locareas->where('locationid in ('.$locid.') ')->order('id desc')->select();

    for($i=0;$i<count($list);$i++){

      $id=$list[$i]['id'];
      $areaname=$list[$i]['areaname'];

      $ilist = $Ipdb_locations->where('id in ('.$locid.') ')->order('id desc')->select();
      $name=$ilist[0]['name'];
      $area=$ilist[0]['area'];
      $list[$i]['area']=$area.'»'.$name.'»';
      $list[$i]['name']='<a href="'.__ROOT__.'/index.php/Home/Racksreport/index/flag/rack/areaid/'.$id.'.html" >'.$areaname.'</a>';

      $rackscount = $Ipdb_racks->where('locareaid ="'.$id.'" ')->order('id desc')->count();
      $list[$i]['rackscount']=$rackscount;
      
      $itemscount = $Ipdb_items->where('locareaid ="'.$id.'" ')->order('id desc')->count();
      $list[$i]['itemscount']=$itemscount;


      $rackslistcount = $Ipdb_racks->where('locareaid ="'.$id.'" ')->field(' sum(usize) as rack_usizecount')->select();
      $rack_usizecount=$rackslistcount[0]['rack_usizecount'];

      $itemslist = $Ipdb_items->where('locareaid ="'.$id.'" ')->field(' sum(usize) as item_usizecount')->select();
      $item_usizecount=$itemslist[0]['item_usizecount'];
      $busy=($item_usizecount/$rack_usizecount)*100;
      $busy=round($busy,2);
      $list[$i]['busy']=$busy.'%';




      
    }

}



if ($flag=='rack'){
    $list = $Ipdb_racks->where('locareaid in ('.$areaid.') ')->order('id desc')->select();

    for($i=0;$i<count($list);$i++){

      $id=$list[$i]['id'];
      $comments=$list[$i]['comments'];
      $locid=$list[$i]['locationid'];
      $racksname=$list[$i]['name'];

      $ilist = $Ipdb_locations->where('id in ('.$locid.') ')->order('id desc')->select();
      $name=$ilist[0]['name'];
      $area=$ilist[0]['area'];
      $list[$i]['area']=$area.'»'.$name.'»';
      $list[$i]['name']=$comments;//'<a href="'.__ROOT__.'/index.php/Home/Racksreport/index/flag/rack/areaid/'.$id.'.html" >'.'</a>'

      $arealist = $Ipdb_locareas->where('id in ('.$areaid.') ')->order('id desc')->select();
      $racksname='<a href="'.__ROOT__.'/index.php/Home/Racks/viewrack/rackid/'.$id.'.html" target="_blank">'.$racksname.'</a>';
      $list[$i]['name']=$arealist[0]['areaname'].'»'.$racksname;//

      $rackscount = $Ipdb_racks->where('id ="'.$id.'" ')->order('id desc')->count();
      $list[$i]['rackscount']=$rackscount;
      
      $itemscount = $Ipdb_items->where('rackid ="'.$id.'" ')->order('id desc')->count();
      $list[$i]['itemscount']=$itemscount;
      
      $rackslistcount = $Ipdb_racks->where('id ="'.$id.'" ')->field(' sum(usize) as rack_usizecount')->select();
      $rack_usizecount=$rackslistcount[0]['rack_usizecount'];

      $itemslist = $Ipdb_items->where('rackid ="'.$id.'" ')->field(' sum(usize) as item_usizecount')->select();
      $item_usizecount=$itemslist[0]['item_usizecount'];
      $busy=($item_usizecount/$rack_usizecount)*100;
      $busy=round($busy,2);
      $list[$i]['busy']=$busy.'%';



      
    }

}








  $this->assign('url_flag','reportmap');
  $this->assign('json_showmenu',$json_showmenu);
  $this->assign('tr',$tr);
  $this->assign('list',$list);
  $this->assign('showlabel',$showlabel);
  $this->display();  
}






public function  reportmap(){

   if(!is_login()){
     $this->redirect("User/login");
   }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');

  $Ipdb_locations =M("ipdb_locations");
  $Ipdb_locareas =M("ipdb_locareas");
  $Ipdb_racks =M("ipdb_racks");
  $Ipdb_items =M("ipdb_items");

  
  $areaid= $_REQUEST['areaid'];
  $locid= $_REQUEST['locid'];
  $title= $_REQUEST['title'];
  $flag= $_REQUEST['flag'];
  $rackid= $_REQUEST['rackid'];
  $location_id= $_REQUEST['location_id'];
  $lids=implode(',',$location_id);

/*
区域  机柜数 设备数 使用率
华北  453 492 8.06%

机房  机柜数 设备数 使用率
北京B28数据中心 125 124 6.86%

*/

  if(empty($title)){
      $list = $Ipdb_locations->group(' area ')->select();
  }else{
    if ($flag=='area'){  
      $list = $Ipdb_locations->where("area like '%$title%' ")->order('id desc')->select();
      $locid='';
      for($j=0;$j<count($list);$j++){
          $id=$list[$j]['id'];
          $locid=$locid.','.$id;
      }
      $locid=substr($locid,1,strlen($locid));
    }

  }



  for($i=0;$i<count($list);$i++){

    $id=$list[$i]['id'];
    $area=$list[$i]['area'];
    $name=$list[$i]['name'];

    $list[$i]['name']='<a href="'.__ROOT__.'/index.php/Home/Racksreport/reportmap/flag/area/title/'.$area.'.html" >'.$area.'</a>';

    if(empty($title)){
        $locidslist=$Ipdb_locations->group('area')->field('group_concat(id) as locids')->select();
        $locids=$locidslist[$i]['locids'];
    }else{
        $locidslist=$Ipdb_locations->where("area like '%$title%' ")->group('area')->field('group_concat(id) as locids')->select();
        $locids=$locidslist[0]['locids'];
    }
    

    $rackscount = $Ipdb_racks->where('locationid in ('.$locids.') ')->order('id desc')->count();
    $list[$i]['rackscount']=$rackscount;


    $itemscount = $Ipdb_items->where('locationid in ('.$locids.') ')->order('id desc')->count();
    $list[$i]['itemscount']=$itemscount;

    $rackslistcount = $Ipdb_racks->where('locationid in ('.$locids.') ')->field(' sum(usize) as rack_usizecount')->select();
    $rack_usizecount=$rackslistcount[0]['rack_usizecount'];

    $itemslist = $Ipdb_items->where('locationid in ('.$locids.') ')->field(' sum(usize) as item_usizecount')->select();
    $item_usizecount=$itemslist[0]['item_usizecount'];
    $busy=($item_usizecount/$rack_usizecount)*100;
    $busy=round($busy,2);
    $list[$i]['busy']=$busy.'%';

    
  }


if ($flag=='area'){
    $list = $Ipdb_locareas->where('locationid in ('.$locid.') ')->order('id desc')->select();

    for($i=0;$i<count($list);$i++){

      $id=$list[$i]['id'];
      $areaname=$list[$i]['areaname'];

      $ilist = $Ipdb_locations->where('id in ('.$locid.') ')->order('id desc')->select();
      $name=$ilist[0]['name'];
      $area=$ilist[0]['area'];
      $list[$i]['area']=$area.'»'.$name.'»';
      $list[$i]['name']='<a href="'.__ROOT__.'/index.php/Home/Racksreport/reportmap/flag/rack/areaid/'.$id.'/title/'.$areaname.'.html" >'.$areaname.'</a>';

      $rackscount = $Ipdb_racks->where('locareaid ="'.$id.'" ')->order('id desc')->count();
      $list[$i]['rackscount']=$rackscount;
      
      $itemscount = $Ipdb_items->where('locareaid ="'.$id.'" ')->order('id desc')->count();
      $list[$i]['itemscount']=$itemscount;


      $rackslistcount = $Ipdb_racks->where('locareaid ="'.$id.'" ')->field(' sum(usize) as rack_usizecount')->select();
      $rack_usizecount=$rackslistcount[0]['rack_usizecount'];

      $itemslist = $Ipdb_items->where('locareaid ="'.$id.'" ')->field(' sum(usize) as item_usizecount')->select();
      $item_usizecount=$itemslist[0]['item_usizecount'];
      $busy=($item_usizecount/$rack_usizecount)*100;
      $busy=round($busy,2);
      $list[$i]['busy']=$busy.'%';




      
    }

}



if ($flag=='rack'){
    $list = $Ipdb_racks->where('locareaid in ('.$areaid.') ')->order('id desc')->select();

    for($i=0;$i<count($list);$i++){

      $id=$list[$i]['id'];
      $comments=$list[$i]['comments'];
      $locid=$list[$i]['locationid'];
      $racksname=$list[$i]['name'];

      $ilist = $Ipdb_locations->where('id in ('.$locid.') ')->order('id desc')->select();
      $name=$ilist[0]['name'];
      $area=$ilist[0]['area'];
      $list[$i]['area']=$area.'»'.$name.'»';
      $list[$i]['name']=$comments;//'<a href="'.__ROOT__.'/index.php/Home/Racksreport/index/flag/rack/areaid/'.$id.'.html" >'.'</a>'

      $arealist = $Ipdb_locareas->where('id in ('.$areaid.') ')->order('id desc')->select();
      $racksname='<a href="'.__ROOT__.'/index.php/Home/Racks/viewrack/rackid/'.$id.'.html" target="_blank">'.$racksname.'</a>';
      $list[$i]['name']=$arealist[0]['areaname'].'»'.$racksname;//

      $rackscount = $Ipdb_racks->where('id ="'.$id.'" ')->order('id desc')->count();
      $list[$i]['rackscount']=$rackscount;
      
      $itemscount = $Ipdb_items->where('rackid ="'.$id.'" ')->order('id desc')->count();
      $list[$i]['itemscount']=$itemscount;
      
      $rackslistcount = $Ipdb_racks->where('id ="'.$id.'" ')->field(' sum(usize) as rack_usizecount')->select();
      $rack_usizecount=$rackslistcount[0]['rack_usizecount'];

      $itemslist = $Ipdb_items->where('rackid ="'.$id.'" ')->field(' sum(usize) as item_usizecount')->select();
      $item_usizecount=$itemslist[0]['item_usizecount'];
      $busy=($item_usizecount/$rack_usizecount)*100;
      $busy=round($busy,2);
      $list[$i]['busy']=$busy.'%';



      
    }

}



  $this->assign('url_flag','reportmap');
  $this->assign('json_showmenu',$json_showmenu);
  $this->assign('flag',$flag);
  $this->assign('title',$title);
  $this->assign('area',$area);
  $this->assign('tr',$tr);
  $this->assign('list',$list);
  $this->assign('showlabel',$showlabel);
  $this->display();  
}


































}?>
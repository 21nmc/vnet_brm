<?php
namespace  Home\Controller;
use Think\Controller;

class RacksController extends HomeController{


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




  public function  listracks(){
      header("Content-Type: text/html; charset=UTF-8");
     if(!is_login()){
       $this->redirect("User/login");
     }    

    $uid        =   is_login();
    $arr_u = get_hostgroup_by_uid($uid);
    
    $nickname = M('user')->getFieldByUid($uid, 'username');

    $Ipdb_racks =M("ipdb_racks");
    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    $Ipdb_items =M("ipdb_items");


    $name= $_REQUEST['name'];
    $depart_id= $_REQUEST['depart_id'];
    if (empty($name) && empty($depart_id)){
        if (IS_ROOT) {
            $count = $Ipdb_racks->count();
            $page = new \Think\Page($count,15);
            $rackslist=$Ipdb_racks->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();  
        }else{
            $condition['departmentid'] = array('in',$arr_u);
            $count = $Ipdb_racks->where($condition)->count();
            $page = new \Think\Page($count,15);
            $rackslist=$Ipdb_racks->where($condition)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }
        
    }else{
        if (IS_ROOT) {
            if ($_REQUEST['name']) {
                $condition['name'] = array('like',"%".$name."%");
            }
            if ($_REQUEST['depart_id']) {
                $condition['departmentid'] = $_REQUEST['depart_id'];
            }
            
            $count = $Ipdb_racks->where($condition)->count();
            $page = new \Think\Page($count,15);
            $rackslist=$Ipdb_racks->where($condition)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            if ($_REQUEST['name']) {
                $condition['name'] = array('like',"%".$name."%");
            }
            if ($_REQUEST['depart_id']) {
                $condition['departmentid'] = $_REQUEST['depart_id'];
            }else{
                $condition['departmentid'] = array('in',$arr_u);
            }
          
            $count = $Ipdb_racks->where($condition)->count();
            $page = new \Think\Page($count,15);
            $rackslist=$Ipdb_racks->where($condition)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }
        
    }
    
    $map['name'] = $_GET['name'];
    foreach($map as $key=>$val) {
        $p->parameter .= "$key=".urlencode($val)."&";
    }
    $page->setConfig('header','共');
    $page->setConfig('first','«');
    $page->setConfig('last','»');
    //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
    $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
    $show = $page->show();
    

    for($i=0;$i<count($rackslist);$i++){
        $rackid=$rackslist[$i]['id']; 
        $locareaid=$rackslist[$i]['locareaid']; 
        $locationid=$rackslist[$i]['locationid']; 

        $loclist=$Ipdb_locations->where('id="'.$locationid.'"')->select();
        $locname=$loclist[0]['name'];
        $rackslist[$i]['locname']=$locname;

        $arealist=$Ipdb_locareas->where('id="'.$locareaid.'"')->select();
        $locareaname=$arealist[0]['areaname'];
        $rackslist[$i]['areaname']=$locareaname;

        $rackcount=$Ipdb_items->where('rackid="'.$rackid.'"')->count();
        $rackslist[$i]['rackcount']=$rackcount;

    }
    
    
    $map_depart['id'] = array('in',$arr_u);
    
    if (IS_ROOT) {
        $depart_list = M('department')->select();
    }else{
        $depart_list = M('department')->where($map_depart)->select();
    }
    
    $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
    $this->assign('depart_list',$depart_list);
    $this->assign('depart_arr_now',$depart_arr_now);


    $this->assign('page',$show);
    $this->assign('url_flag','listracks');
    $this->assign('list',$rackslist);
    $this->assign('areaname',$areaname);
    $this->display();   
  } 




  public function  rackslog(){
 
     if(!is_login()){
       $this->redirect("User/login");
     }    

    $uid        =   is_login();
    $nickname = M('user')->getFieldByUid($uid, 'username');

    $Ipdb_racks =M("ipdb_racks");
    $Ipdb_rackslog =M("ipdb_rackslog");
    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    $Ipdb_items =M("ipdb_items");

    $rackid= $_REQUEST['rackid'];
  
        $racksloglist=$Ipdb_rackslog->where('rackid = "'.$rackid.'" ')->order('id desc')->select();

    for($i=0;$i<count($racksloglist);$i++){
        $id=$racksloglist[$i]['id']; 
        $tempflag=$racksloglist[$i]['flag']; 
        $temprackid=$racksloglist[$i]['rackid']; 
        $log_uid=$racksloglist[$i]['uid']; 
        $locationid=$racksloglist[$i]['locationid']; 
        $itemid=$racksloglist[$i]['itemid']; 

        $log_nickname_info = M('user')->where("id = $log_uid")->find();// by zwj 操作用户
        $racksloglist[$i]['nickname']=$log_nickname_info['username'];

        $rlist=$Ipdb_racks->where('id="'.$temprackid.'"')->select();
        $comments=$rlist[0]['name'];   

        $racksloglist[$i]['comments']=$comments;
        if ($tempflag=='up'){$racksloglist[$i]['flag']='上架';}elseif ($tempflag=='down'){$racksloglist[$i]['flag']='下架';}elseif ($tempflag=='move'){$racksloglist[$i]['flag']='迁移';}elseif ($tempflag=='obligate'){$racksloglist[$i]['flag']='预留';}else{$racksloglist[$i]['flag']='其他';}
        
        $ilist=$Ipdb_items->where('id="'.$itemid.'"')->select();
        $label=$ilist[0]['common_name'];   
        $racksloglist[$i]['label']=$label;

    }

    $this->assign('url_flag','listracks');
    $this->assign('list',$racksloglist);
    $this->display();   
  } 











public function addrack(){

    if(!is_login()){
        $this->redirect("User/login");
    }   
    $uid        =   is_login();
    $nickname = M('user')->getFieldByUid($uid, 'username');

    $Ipdb_racks =M("ipdb_racks");
    $Department =M("department");
    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    $Ipdb_items =M("ipdb_items");
    $Ipdb_node =M("ipdb_node");


    $departlist=$Department->order('id desc')->select();
    $departmentoption= '<option value ="" >请选择部门...</option>';
    for($i=0;$i<count($departlist);$i++){      
        $depid=$departlist[$i]['id'];
        $depname=$departlist[$i]['depart_name'];
        $departmentoption.='<option value ="'.$depid.'" >'.$depname.'</option>';
    }

    $list=$Ipdb_locations->select();
    $str_= '<option value ="" >请选择数据中心...</option>';
    for($i=0;$i<count($list);$i++){      
        $id=$list[$i]['id'];
        $locname=$list[$i]['name'];
        $str_.='<option value ="'.$id.'" >'.$locname.'</option>';
    }
    $list2=$Ipdb_locareas->field('id,areaname,locationid')->select();
    $jsonlist2 = json_encode($list2);
    
    $usizelist="";
    for($i=50;$i>0;$i--){
        $usizelist.="<option  value='".$i."'>".$i."U</option>";
    }

//---------------------------------------标记-------------------------------------------
    
    $aDataList=$Ipdb_node->order(' id asc')->select();//where('contypeid=0')->

    $aDataList = $this->array_column($aDataList, null, 'id');

    $aTreeList = $this->getTreeList(0, $aDataList);//获取 下面 层次 用 getTreeList
    $contypeoption = $this->createOptions($aTreeList);
//---------------------------------------标记-------------------------------------------


    if(IS_POST){

      $money=I('post.money');
      $lease_mode=I('post.lease_mode');
      $power=I('post.power');
      $departmentid=I('post.departmentid');
      $revnums=I('post.revnums');
      $locationid=I('post.locationid');
      $usize=I('post.usize');
      $depth=I('post.depth');
      $comments=I('post.comments');
      $model=I('post.model');
      $label=I('post.label');
      $areaid=I('post.areaid');
      $name=I('post.name');
      $contypeid=I('post.contypeid');
      if(empty($contypeid)){$this->error('标记不能为空!');}
      if(empty($name)){$this->error('名称不能为空!');}
      //if(empty($depth)){$this->error('深度不能为空!');}
      if(empty($usize)){$this->error('高度不能为空!');}

      // $itemcount=$Ipdb_items->where('rackid="'.$rackid.'"')->count();
      // $rackslist[$i]['rackcount']=$rackcount;

      $adddata['nodeid']=I('post.contypeid');
      $adddata['name']=I('post.name');
      $adddata['money']=I('post.money');
      $adddata['power']=I('post.power');
      $adddata['lease_mode']=I('post.lease_mode');
      $adddata['departmentid']=I('post.departmentid');
      $adddata['label']=I('post.label');
      $adddata['locationid']=I('post.locationid');
      $adddata['usize']=I('post.usize');
      $adddata['depth']=I('post.depth');
      $adddata['comments']=I('post.comments');
      $adddata['model']=I('post.model');
      $adddata['locareaid']=I('post.areaid');
      $adddata['revnums']=I('post.revnums');
      $adddata['start_time']=I('post.start_time');
      $adddata['end_time']=I('post.end_time');

      if($Ipdb_racks->add($adddata)){
          $this->show('<script type="text/javascript" >alert("机柜添加成功");window.location.href="{:U("listracks")}"; </script>');
      }else{
          $this->show('<script type="text/javascript" >alert("机柜添加失败");window.location.href="{:U("addrack")}"; </script>');
      }
      exit();
    }


    $this->assign('url_flag','listracks');
    $this->assign('uid',$uid);
    $this->assign('list',$str_);
    $this->assign('departmentoption',$departmentoption);
    $this->assign('usizelist',$usizelist);
    $this->assign('jsonlist2',$jsonlist2);
    $this->assign('nickname',$nickname);
    $this->assign('contypeoption',$contypeoption);
    $this->display();
  }







public function editlistracks(){

    if(!is_login()){
        $this->redirect("User/login");
    }   
    $uid        =   is_login();
    $nickname = M('user')->getFieldByUid($uid, 'username');

    $Ipdb_racks =M("ipdb_racks");
    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    $Ipdb_items =M("ipdb_items");
    $Department =M("department");
    $Ipdb_node =M("ipdb_node");

    $id=I('get.id');
    empty($id) && $this->error('ID参数不能为空'); 
//---------------------------------获取参数值---------------------------------------
    $rackslist=$Ipdb_racks->where('id = "'.$id.'" ')->order('id desc')->select();
    $lease_mode=$rackslist[0]['lease_mode'];
    $name=$rackslist[0]['name'];
    $money=$rackslist[0]['money'];
    $power=$rackslist[0]['power'];
    $start_time=$rackslist[0]['start_time'];
    $end_time=$rackslist[0]['end_time'];
    $locareaid=$rackslist[0]['locareaid'];
    $locationid=$rackslist[0]['locationid'];
    $rackid=$rackslist[0]['id'];
    $comments=$rackslist[0]['comments'];
    $depth=$rackslist[0]['depth'];
    $model=$rackslist[0]['model'];
    $label=$rackslist[0]['label'];
    $revnums=$rackslist[0]['revnums'];
    $usize=$rackslist[0]['usize'];
    $departmentid=$rackslist[0]['departmentid'];
    $nodeid=$rackslist[0]['nodeid'];

    $itemcount=$Ipdb_items->where('rackid="'.$rackid.'"')->count();
    $llist=$Ipdb_locations->where('id = "'.$locationid.'" ')->order('id desc')->select();
    $tempname=$llist[0]['name'];

    $llist2=$Ipdb_locareas->where('id = "'.$locareaid.'" ')->order('id desc')->select();
    $tempareaname=$llist2[0]['areaname'];

//------------------------------------------------------------------------
    

    $departlist=$Department->order('id desc')->select();
    $departmentoption= '<option value ="" >请选择部门...</option>';
    for($i=0;$i<count($departlist);$i++){      
        $depid=$departlist[$i]['id'];
        $depname=$departlist[$i]['depart_name'];
        if ($depid==$departmentid){
           $departmentoption.='<option value ="'.$depid.'" selected="selected">'.$depname.'</option>';
        }else{
           $departmentoption.='<option value ="'.$depid.'" >'.$depname.'</option>';
        }
        
    }



    $list=$Ipdb_locations->select();
    $str_= '<option value ="" >请选择数据中心...</option>';
    for($i=0;$i<count($list);$i++){      
        $id=$list[$i]['id'];
        $locname=$list[$i]['name'];
        if ($locationid==$id){
            $str_.='<option value ="'.$id.'" selected="selected">'.$locname.'</option>';
        }else{
            $str_.='<option value ="'.$id.'" >'.$locname.'</option>';
        }
        
    }
    $list2=$Ipdb_locareas->field('id,areaname,locationid')->select();
    $jsonlist2 = json_encode($list2);

    $usizelist="";
    for($i=50;$i>0;$i--){

      if ($usize==$i){
          $usizelist.="<option  value='".$i."' selected='selected'>".$i."U</option>";
      }else{
          $usizelist.="<option  value='".$i."'>".$i."U</option>";
      }
    }



//---------------------------------------标记-------------------------------------------
    
    $aDataList=$Ipdb_node->order(' id asc')->select();//where('contypeid=0')->

    $aDataList = $this->array_column($aDataList, null, 'id');

    $aTreeList = $this->getTreeList(0, $aDataList);//获取 下面 层次 用 getTreeList
    $contypeoption = $this->createOptions($aTreeList,$nodeid);
//---------------------------------------标记-------------------------------------------


    if(IS_POST){

      $id=I('get.id');
      empty($id) && $this->error('ID参数不能为空'); 

      $usize=I('post.usize');
      $depth=I('post.depth');
      $name=I('post.name');
      $contypeid=I('post.contypeid');
      if(empty($contypeid)){$this->error('标记不能为空!');}
      if(empty($depth)){$this->error('深度不能为空!');}
      if(empty($name)){$this->error('名称不能为空!');}
      if(empty($usize)){$this->error('高度不能为空!');}

      $editdata['name']=I('post.name');
      $editdata['lease_mode']=I('post.lease_mode');
      $editdata['money']=I('post.money');
      $editdata['power']=I('post.power');
      $editdata['start_time']=I('post.start_time');
      $editdata['end_time']=I('post.end_time');
      $editdata['label']=I('post.label');
      $editdata['usize']=I('post.usize');
      $editdata['depth']=I('post.depth');
      $editdata['comments']=I('post.comments');
      $editdata['model']=I('post.model');
      $editdata['departmentid']=I('post.departmentid');
      $editdata['revnums']=I('post.revnums');
      $editdata['nodeid']=I('post.contypeid');
      $Ipdb_racks->where('id="'.$id.'"')->setField($editdata);
      $this->show('<script type="text/javascript" >alert("机柜修改成功");window.location.href="{:U("listracks")}"; </script>');
      exit();

    }

    $this->assign('url_flag','listracks');

    $this->assign('uid',$uid);
    $this->assign('list',$str_);
    $this->assign('usizelist',$usizelist);
    $this->assign('jsonlist2',$jsonlist2);
    $this->assign('jsonlist3',$jsonlist3);
    $this->assign('nickname',$nickname);
    $this->assign('name',$name);
    $this->assign('usize',$usize);
    $this->assign('itemcount',$itemcount);
    $this->assign('comments',$comments);
    $this->assign('depth',$depth);
    $this->assign('revnums',$revnums);
    $this->assign('model',$model);
    $this->assign('label',$label);
    $this->assign('inused',$inused);
    $this->assign('tempname',$tempname);
    $this->assign('tempareaname',$tempareaname);
    $this->assign('departmentoption',$departmentoption);
    $this->assign('lease_mode',$lease_mode);
    $this->assign('money',$money);
    $this->assign('power',$power);
    $this->assign('start_time',$start_time);
    $this->assign('end_time',$end_time);
    $this->assign('contypeoption',$contypeoption);
    $this->display();
  }






public function  dellistracks(){

  if(!is_login()){
    $this->redirect("User/login");
  }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');
  $Ipdb_racks =M("ipdb_racks");

  $id=I('get.id');
  empty($id) && $this->error('id不能为空'); 

  $Ipdb_racks->where('id="'.$id.'"')->delete();
  $this->show('<script type="text/javascript" >alert("机柜删除成功");window.location.href="{:U("listracks")}"; </script>'); 
  
}


public function  rackspace(){

  if(!is_login()){
     $this->redirect("User/login");
  }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');


  $Ipdb_items =M("ipdb_items");
  $Department =M("department");
  $Itemtype =M("itemtype");
  $Ipdb_racks =M("ipdb_racks");
  $Ipdb_locations =M("ipdb_locations");
  $Ipdb_locareas =M("ipdb_locareas");



  $objectid= $_REQUEST['objectid'];
  $flag= $_REQUEST['flag'];
  empty($objectid) && $this->error('请输入id');


  if ($flag=='obligateok'){
    $okdata['appointment']=0;
    $Ipdb_items->where('id="'.$objectid.'"')->setField($okdata);
    $this->show('<script type="text/javascript" >alert("设备预留变更为上架成功");window.location.href="{:U(\"Racks/rackspace?objectid='.$objectid.'\")}"; </script>');
    exit();
  }
    



  $list = $Ipdb_items->where('id = "'.$objectid.'"')->select();
  $label=$list[0]['label'];
  $ip=$list[0]['ip'];
  $sn=$list[0]['sn'];
  $comments=$list[0]['comments'];
  $usize=$list[0]['usize'];
  $rackid=$list[0]['rackid'];
  $rackposition=$list[0]['rackposition'];
  $system=$list[0]['system'];
  $model=$list[0]['model'];
  $rackposdepth=$list[0]['rackposdepth'];
  $manufacturerid=$list[0]['manufacturerid'];
  $locareaid=$list[0]['locareaid'];
  $locationid=$list[0]['locationid'];
  $appointment=$list[0]['appointment'];

  if ($appointment==1){$status='<font color="#FFD700">预留</font> &nbsp;&nbsp;>> &nbsp;&nbsp;<a href="'.__ROOT__.'/index.php/Home/Racks/rackspace/flag/obligateok/objectid/'.$objectid.'.html">变更上架</a>';}
  if (empty($locationid) && empty($locareaid) && empty($rackid)){$status='<font color="">下架</font>';}
  if (!empty($locationid) && empty($appointment) && !empty($locareaid) && !empty($rackid)){$status='<font color="#00FFFF">上架</font>';}



  $itemtypeid=$list[0]['itemtypeid'];
  $typelist = $Itemtype->where('id = "'.$itemtypeid.'"')->select();
  $typedesc=$typelist[0]['typedesc'];

  $departmentid=$list[0]['departmentid'];
  $dlist = $Department->where('id = "'.$departmentid.'"')->select();
  $dname=$dlist[0]['name'];

  $racklist = $Ipdb_racks->where('id = "'.$rackid.'"')->select();
  $usizecount=$racklist[0]['usize'];
  $revnums=$racklist[0]['revnums'];
  $rackcomments=$racklist[0]['comments'];

  $locationlist = $Ipdb_locations->where('id = "'.$locationid.'"')->select();
  $location_name=$locationlist[0]['name'];
  $locarealist = $Ipdb_locareas->where('id = "'.$locareaid.'"')->select();
  $areaname=$locarealist[0]['areaname'];


  $racktable=$this->showeditrackposdepth($rackid,$objectid); 

  if(empty($usizecount)){
      $usizelist="";
      for($i=1;$i<51;$i++){
          $usizelist.="<option  value='".$i."'>".$i."U</option>";
      }

      $itemusizeoption=$usizelist;


  }else{
      //---------------------------------U位选择
        $usizelist="";
        for($i=1;$i<=$usizecount;$i++){
          if ($i==$rackposition){
            $usizelist.="<option  value='".$i."' selected='selected'>".$i."U</option>";
          }else{
            $usizelist.="<option  value='".$i."'>".$i."U</option>";
          }
        }  

      //---------------------------------机柜位置选择
      $itemusizeoption="";
      for($i=1;$i<=$usizecount;$i++){
        if ($i==$usize){
          $itemusizeoption.="<option  value='".$i."' selected='selected'>".$i."U</option>";
        }else{
          $itemusizeoption.="<option  value='".$i."'>".$i."U</option>";
        }
      }

  }






//---------------------------------机柜选择
  $racklist = $Ipdb_racks->select();
  $option='';
  $racklist=$Ipdb_racks->order('id asc')->select();
  $rackoption= '<option value ="" >请选择机柜...</option>';
  for($i=0;$i<count($racklist);$i++){      
      $depid=$racklist[$i]['id'];
      $tempcomments=$racklist[$i]['comments'];
      if ($depid==$rackid){
        $rackoption.='<option value ="'.$depid.'" selected="selected">'.$tempcomments.'</option>';
      }else{
        $rackoption.='<option value ="'.$depid.'" >'.$tempcomments.'</option>';
      }
      
  }


  if(IS_POST){
      $flag= $_REQUEST['flag'];//设备id

    if ($flag=='up'){
        $objectid= $_REQUEST['objectid'];//设备id
        $rackposdepthid= $_REQUEST['rackposdepthid'];//机柜深度id
        $rackposition= $_REQUEST['rackposition'];//设备起始U位
        $myusize= $_REQUEST['myusize'];//设备U大小
        $locationid= $_REQUEST['locationid'];//机房id
        $areaid= $_REQUEST['areaid'];//房间ID
        $rackid= $_REQUEST['rackid'];//机柜ID

        empty($objectid) && $this->error('请输入设备ID!');
        empty($rackposdepthid) && $this->error('请输入机柜深度!');
        empty($rackposition) && $this->error('请输入设备起始U位!');
        empty($myusize) && $this->error('请输入设备U大小!');
        empty($locationid) && $this->error('请输入机房ID!');
        empty($areaid) && $this->error('请输入房间ID!');
        empty($rackid) && $this->error('请输入机柜ID!');

//-----------------------------------判断U位是否占用 start----------------------------------------------
        $uCount=($rackposition+$myusize)-1;
        
        $checkitemlist = $Ipdb_items->where('rackid = "'.$rackid.'" ')->select();
        for($i=0;$i<count($checkitemlist);$i++){      
            $checkusize=$checkitemlist[$i]['usize'];
            $checkrackposition=$checkitemlist[$i]['rackposition'];
            
            $checkuCount=($checkrackposition+$checkusize)-1;
            $forcount=$checkuCount-$checkrackposition;

            unset($check);
            for($ii=0;$ii<=$forcount;$ii++){ //2-5 共4U
                $check=$rackposition+$ii;
                $check2=$checkrackposition+$ii;
                //向下兼容排查报错
                if ($check==$checkrackposition){$this->error('机柜U位已经被占用，请重新选择!');} //如果是 起始2  目的 5  2和5异常，但不包含3-4
                //向上兼容排查报错
                if ($check==$checkuCount){$this->error('机柜U位已经被占用，请重新选择!');}
                //起点相同可以判断异常
                //if ($check==$check2){$this->error('机柜U位已经被占用3，请重新选择!');}
            }

        }
//-----------------------------------判断U位是否占用 end-------------------------------------------------


//-----------------------------------log start--------------------------------------------
        $this->putrackslog($rackid,$objectid,$flag);
//-----------------------------------log end----------------------------------------------

        unset($adddata);
        $adddata['rackposdepth']=I('post.rackposdepthid');
        $adddata['usize']=I('post.myusize');
        $adddata['rackposition']=I('post.rackposition');
        $adddata['rackid']=I('post.rackid');
        $adddata['locationid']=$locationid;
        $adddata['rackid']=$rackid;
        $adddata['locareaid']=$areaid;
        $adddata['appointment']=0; //1=预留 0是正常

        $Ipdb_items->where('id="'.$objectid.'"')->setField($adddata);
        $this->show('<script type="text/javascript" >alert("设备上架变更成功");window.location.href="{:U(\"Racks/rackspace?objectid='.$objectid.'\")}"; </script>');
        exit();
    }elseif($flag=='down'){
        $objectid= $_REQUEST['objectid'];//设备id
        empty($objectid) && $this->error('请输入设备ID!');

//-----------------------------------log start--------------------------------------------
        $this->putrackslog($rackid,$objectid,$flag);
//-----------------------------------log end----------------------------------------------

        unset($downdata);
        $downdata['rackposdepth']='';
        $downdata['rackposition']='';
        $downdata['locationid']='';
        $downdata['rackid']='';
        $downdata['locareaid']='';
        $downdata['appointment']=0;

        $Ipdb_items->where('id="'.$objectid.'"')->setField($downdata);
        $this->show('<script type="text/javascript" >alert("设备下架操作成功");window.location.href="{:U(\"Racks/rackspace?objectid='.$objectid.'\")}"; </script>');
        exit();


    }elseif($flag=='move'){

        $objectid= $_REQUEST['objectid'];//设备id
        $myusize= $_REQUEST['myusize'];//设备U大小
        $locationid= $_REQUEST['locationid'];//机房id
        $areaid= $_REQUEST['areaid'];//房间ID
        $rackid= $_REQUEST['rackid'];//机柜ID
        $rackposition= $_REQUEST['rackposition'];//机架起始位置
        $rackposdepthid= $_REQUEST['rackposdepthid'];//机架深度ID
        empty($objectid) && $this->error('请输入设备ID!');
        empty($myusize) && $this->error('请输入设备U大小!');
        empty($locationid) && $this->error('请输入机房ID!');
        empty($areaid) && $this->error('请输入房间ID!');
        empty($rackid) && $this->error('请输入机柜ID!');
        empty($rackposition) && $this->error('请输入机架起始位置!');
        empty($rackposdepthid) && $this->error('请输入机架深度ID!');
        


//-----------------------------------判断U位是否占用 start----------------------------------------------

//--------------------获取当前设备要占U位id开始

        $mystart=$rackposition;
        if ($myusize==1){
          $myend=($rackposition+$myusize);
        }else{
          $myend=($rackposition+$myusize)-1;
        }
        //$mycount=$myend-$mystart;
        $my_alls='';
        for($j=0;$j<$myusize;$j++){ 
           $check_myrackposition=$mystart+$j;
           $my_alls=$my_alls.','.$check_myrackposition;
        }
        $my_alls=substr($my_alls,1,strlen($my_alls));
//--------------------获取当前设备要占U位id结束
        //echo $my_alls."------------<br />";


        $checkitemlist = $Ipdb_items->where('rackid = "'.$rackid.'" ')->select();
        $other_alls='';
        for($i=0;$i<count($checkitemlist);$i++){      
            $other_usize=$checkitemlist[$i]['usize'];
            $other_start=$checkitemlist[$i]['rackposition'];
            $other_end=($other_start+$other_usize)-1;

            $other_all='';
            for($ii=0;$ii<$other_usize;$ii++){ //2-5 共4U
                $check_otherrackposition=$other_start+$ii;
                $other_all=$other_all.','.$check_otherrackposition;
            }
            $other_all=substr($other_all,1,strlen($other_all));
            $other_alls=$other_alls.','.$other_all;
        }
        $other_alls=substr($other_alls,1,strlen($other_alls));

        //echo $other_alls."------------<br />";
        $my_allarrays=explode(',',$my_alls); 
        $other_allarrays=explode(',',$other_alls); 

        $my=array_intersect($my_allarrays,$other_allarrays);
        //var_dump($my);
        if (!empty($my)){
            $this->show('<script type="text/javascript" >alert("机柜U位已经被占用，请重新选择!");history.go(-1)</script>');
            exit();
        }
//-----------------------------------判断U位是否占用 end-------------------------------------------------


//-----------------------------------log start----------------------------------------------
        $movearray['moveareaid']=$areaid;
        $movearray['movelocationid']=$locationid;
        $movearray['moverackposition']=$rackposition;
        $movearray['movemyusize']=$myusize;
        $movearray['moverackid']=$rackid;

        $this->putrackslog($rackid,$objectid,$flag,$movearray);
//-----------------------------------log end----------------------------------------------


        unset($editddata);
        $editddata['rackposdepth']=$rackposdepthid;
        $editddata['rackposition']=$rackposition;
        $editddata['locationid']=$locationid;
        $editddata['rackid']=$rackid;
        $editddata['locareaid']=$areaid;
        $editddata['appointment']=0;

        $Ipdb_items->where('id="'.$objectid.'"')->setField($editddata);
        $this->show('<script type="text/javascript" >alert("迁移变更成功");window.location.href="{:U(\"Item/item_info?id='.$objectid.'\")}"; </script>');
        exit();

    }elseif ($flag=='obligate'){
        $objectid= $_REQUEST['objectid'];//设备id
        $rackposdepthid= $_REQUEST['rackposdepthid'];//机柜深度id
        $rackposition= $_REQUEST['rackposition'];//设备起始U位
        $myusize= $_REQUEST['myusize'];//设备U大小
        $locationid= $_REQUEST['locationid'];//机房id
        $areaid= $_REQUEST['areaid'];//房间ID
        $rackid= $_REQUEST['rackid'];//机柜ID

        empty($objectid) && $this->error('请输入设备ID!');
        empty($rackposdepthid) && $this->error('请输入机柜深度!');
        empty($rackposition) && $this->error('请输入设备起始U位!');
        empty($myusize) && $this->error('请输入设备U大小!');
        empty($locationid) && $this->error('请输入机房ID!');
        empty($areaid) && $this->error('请输入房间ID!');
        empty($rackid) && $this->error('请输入机柜ID!');

//-----------------------------------判断U位是否占用 start----------------------------------------------

//--------------------获取当前设备要占U位id开始

        $mystart=$rackposition;
        if ($myusize==1){
          $myend=($rackposition+$myusize);
        }else{
          $myend=($rackposition+$myusize)-1;
        }
        //$mycount=$myend-$mystart;
        $my_alls='';
        for($j=0;$j<$myusize;$j++){ 
           $check_myrackposition=$mystart+$j;
           $my_alls=$my_alls.','.$check_myrackposition;
        }
        $my_alls=substr($my_alls,1,strlen($my_alls));
//--------------------获取当前设备要占U位id结束
        //echo $my_alls."------------<br />";


        $checkitemlist = $Ipdb_items->where('rackid = "'.$rackid.'" ')->select();
        $other_alls='';
        for($i=0;$i<count($checkitemlist);$i++){      
            $other_usize=$checkitemlist[$i]['usize'];
            $other_start=$checkitemlist[$i]['rackposition'];
            $other_end=($other_start+$other_usize)-1;

            $other_all='';
            for($ii=0;$ii<$other_usize;$ii++){ //2-5 共4U
                $check_otherrackposition=$other_start+$ii;
                $other_all=$other_all.','.$check_otherrackposition;
            }
            $other_all=substr($other_all,1,strlen($other_all));
            $other_alls=$other_alls.','.$other_all;
        }
        $other_alls=substr($other_alls,1,strlen($other_alls));

        //echo $other_alls."------------<br />";
        $my_allarrays=explode(',',$my_alls); 
        $other_allarrays=explode(',',$other_alls); 

        $my=array_intersect($my_allarrays,$other_allarrays);
        //var_dump($my);
        if (!empty($my)){
            $this->show('<script type="text/javascript" >alert("机柜U位已经被占用，请重新选择!");history.go(-1)</script>');
            exit();
        }
//-----------------------------------判断U位是否占用 end-------------------------------------------------

//-----------------------------------log start--------------------------------------------
        $this->putrackslog($rackid,$objectid,$flag);
//-----------------------------------log end----------------------------------------------

        unset($resdata);
        $resdata['rackposdepth']=I('post.rackposdepthid');
        $resdata['usize']=I('post.myusize');
        $resdata['rackposition']=I('post.rackposition');
        $resdata['rackid']=I('post.rackid');
        $resdata['locationid']=$locationid;
        $resdata['rackid']=$rackid;
        $resdata['locareaid']=$areaid;
        $resdata['appointment']=1;

        $Ipdb_items->where('id="'.$objectid.'"')->setField($resdata);
        $this->show('<script type="text/javascript" >alert("设备预留成功");window.location.href="{:U(\"Racks/rackspace?objectid='.$objectid.'\")}"; </script>');
        exit();
    }elseif ($flag=='move1'){

        $objectid= $_REQUEST['objectid'];//设备id
        $usize= $_REQUEST['myusize'];//设备U大小
        $movelocationid= $_REQUEST['locationid'];//机房id
        $moveareaid= $_REQUEST['areaid'];//房间ID
        $moverackid= $_REQUEST['rackid'];//机柜ID

        empty($objectid) && $this->error('请输入设备ID!');
        empty($usize) && $this->error('请输入设备U大小!');
        empty($movelocationid) && $this->error('请输入机房ID!');
        empty($moveareaid) && $this->error('请输入房间ID!');
        empty($moverackid) && $this->error('请输入机柜ID!');

        $mlocarealist = $Ipdb_locareas->where('id = "'.$moveareaid.'"')->select();
        $moveareaname=$mlocarealist[0]['areaname'];

        $mracklist = $Ipdb_racks->where('id = "'.$moverackid.'"')->select();
        $movecomments=$mracklist[0]['comments'];

        $mlocationlist = $Ipdb_locations->where('id = "'.$movelocationid.'"')->select();
        $movelocationname=$mlocationlist[0]['name'];

//---------------------------------------------------------------show 机柜信息
    
        $moveracktable=$this->showrackposdepth($moverackid,$objectid);

//------------------------------------------------------------------机柜信息结束        



    }






  }


//-----------------------------------------------一二级联动
  $loclist=$Ipdb_locations->select();
  $str_= '<option value ="" >请选择数据中心...</option>';
  for($i=0;$i<count($loclist);$i++){      
      $id=$loclist[$i]['id'];
      $locname=$loclist[$i]['name'];
      $str_.='<option value ="'.$id.'" >'.$locname.'</option>';
  }
  $list2=$Ipdb_locareas->field('id,areaname,locationid')->select();
  $jsonlist2 = json_encode($list2);

  $list3=$Ipdb_racks->field('id,locationid,locareaid,comments')->select();
  $jsonlist3 = json_encode($list3);

  $moveusizelist="";
  for($i=1;$i<51;$i++){
      $moveusizelist.="<option  value='".$i."'>".$i."U</option>";
  }

  if (!empty($usize)){
      $myusize=$usize;
  }


  $this->assign('url_flag','listracks');
  $this->assign('areaname',$areaname);
  $this->assign('location_name',$location_name);
  $this->assign('objectid',$objectid);
  $this->assign('rackoption',$rackoption);
  $this->assign('itemusizeoption',$itemusizeoption);
  $this->assign('label',$label);
  $this->assign('rackposdepth',$rackposdepth);
  $this->assign('ip',$ip);
  $this->assign('sn',$sn);
  $this->assign('status',$status);
  $this->assign('system',$system);
  $this->assign('model',$model);
  $this->assign('manufacturerid',$manufacturerid);
  $this->assign('typedesc',$typedesc);
  $this->assign('dname',$dname);
  $this->assign('comments',$comments);
  $this->assign('rackcomments',$rackcomments);
  $this->assign('racktable',$racktable);
  $this->assign('usizelist',$usizelist);
  $this->assign('moveusizelist',$moveusizelist);
  $this->assign('jsonlist2',$jsonlist2);
  $this->assign('jsonlist3',$jsonlist3);
  $this->assign('loclist',$str_);
  $this->assign('myusize',$usize);

//---------------flag=move1
  $this->assign('movelocationid',$movelocationid);
  $this->assign('moveareaid',$moveareaid);
  $this->assign('moverackid',$moverackid);
  $this->assign('movelocationname',$movelocationname);
  $this->assign('moveareaname',$moveareaname);
  $this->assign('movecomments',$movecomments);
  $this->assign('moveracktable',$moveracktable);
  $this->assign('flag',$flag);
  
//---------------flag=move1

  $this->display();  
}




public function  showeditrackposdepth($rackid,$objectid){

  $Ipdb_items =M("ipdb_items");
  $Ipdb_racks =M("ipdb_racks");

  empty($objectid) && $this->error('请输入ID');
  //empty($rackid) && $this->error('请输入机柜ID');

  $list = $Ipdb_items->where('id = "'.$objectid.'"')->select();
  $common_name=$list[0]['common_name'];
  $ip=$list[0]['ip'];
  $usize=$list[0]['usize'];
  $rackposition=$list[0]['rackposition'];
  $racklocationid=$list[0]['locationid'];
  $racklocareaid=$list[0]['locareaid'];
  $rackposdepth=$list[0]['rackposdepth'];
  $appointment=$list[0]['appointment'];//是否预留状态=1

  $racklist = $Ipdb_racks->where('id = "'.$rackid.'"')->select();
  $usizecount=$racklist[0]['usize'];

  $racktable='';
  $lastpos=$rackposition+$usize;

  $itemlist = $Ipdb_items->where('rackid = "'.$rackid.'" and id not in('.$objectid.')')->select();
  for($j=0;$j<count($itemlist);$j++){
      $itemlist[$j]['lastpos']=$itemlist[$j]['usize']+$itemlist[$j]['rackposition'];
  }

  $allitemlist = $Ipdb_items->where('rackid = "'.$rackid.'" ')->select();
  $ids='';
  for($j=0;$j<count($allitemlist);$j++){
      $Tempusize=$allitemlist[$j]['usize'];
      $Temprackposition=$allitemlist[$j]['rackposition'];
      if ($Tempusize==1){$ids=$ids.','.$Temprackposition;}//1U 
      if ($Tempusize>1){//多U
          $Templastpos=$Tempusize+$Temprackposition;
          for($jj=$Temprackposition;$jj<$Templastpos;$jj++){
              $ids=$ids.','.$jj;
          }
      } 

  }
$ids=substr($ids,1,strlen($ids));//占用的u位起始和结束

  // unset($temp);
  $temp= explode(',',$ids);
  for($ii=0;$ii<count($temp);$ii++){
    $temp[$ii] = intval($temp[$ii]);
  }


  //for($i=1;$i<=$usizecount;$i++){
  for($i=$usizecount;$i>=1;$i--){
//-------------------------------------当前服务器机柜占用情况

    //预留状态颜色判断
    if ($appointment==1){$color='#FFD700';}else{$color='#00FFFF';}

    if ($rackposdepth==1 ){  //后

      if ($i>=$rackposition and $i<$lastpos){
            $racktable.='<tr align="middle" >';
            $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
            $racktable.='<td class="emptyrow" ></td>';
            $racktable.='<td class="emptyrow" ></td>';
            $startrackpostion=($rackposition+$usize)-1;
            if($i==$startrackpostion){
              $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$usize.'" style="background-color:'.$color.'">'.$common_name.'</td>';
            }
            //$racktable.= $label;
            $racktable.='</tr>'; 
      }

    }elseif($rackposdepth==2){ //中

      if ($i>=$rackposition and $i<$lastpos){
            $racktable.='<tr align="middle" >';
            $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
            $racktable.='<td class="emptyrow" ></td>';
            $startrackpostion=($rackposition+$usize)-1;
            if($i==$startrackpostion){
              $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$usize.'" style="background-color:'.$color.'">'.$common_name.'</td>'; 
            }
            $racktable.='<td class="emptyrow" ></td>';
            //$racktable.= $label;
            $racktable.='</tr>'; 
      }


    }elseif($rackposdepth==3){ //中后
        
      if ($i>=$rackposition and $i<$lastpos){
            $racktable.='<tr align="middle" >';
            $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
            $racktable.='<td class="emptyrow" ></td>';
            $startrackpostion=($rackposition+$usize)-1;
            if($i==$startrackpostion){
              $racktable.='<td class="emptyrow" colspan="2" rowspan="'.$usize.'" style="background-color:'.$color.'">'.$common_name.'</td>'; 
            }
            $racktable.='</tr>';

      }

    }elseif($rackposdepth==4){ //前
        
      if ($i>=$rackposition and $i<$lastpos){
            $racktable.='<tr align="middle" >';
            $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
            $startrackpostion=($rackposition+$usize)-1;
            if($i==$startrackpostion){
              $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$usize.'" style="background-color:'.$color.'">'.$common_name.'</td>'; 
            }
            $racktable.='<td class="emptyrow" ></td>';
            $racktable.='<td class="emptyrow" ></td>';
            $racktable.='</tr>';

      }

    }elseif($rackposdepth==5){ //前中
        
      if ($i>=$rackposition and $i<$lastpos){
            $racktable.='<tr align="middle" >';
            $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
            $startrackpostion=($rackposition+$usize)-1;
            if($i==$startrackpostion){
              $racktable.='<td class="emptyrow" colspan="2" rowspan="'.$usize.'" style="background-color:'.$color.'">'.$common_name.'</td>'; 
            }
            $racktable.='<td class="emptyrow" ></td>';
            $racktable.='</tr>';

      }

    }elseif($rackposdepth==6){ //前中后

      if ($i>=$rackposition and $i<$lastpos){
            $racktable.='<tr align="middle" >';
            $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
            $startrackpostion=($rackposition+$usize)-1;
            if($i==$startrackpostion){
              $racktable.='<td class="emptyrow" colspan="3" rowspan="'.$usize.'" style="background-color:'.$color.'">'.$common_name.'</td>'; 
            }
            $racktable.='</tr>';

      }

    }

//-------------------------------------其他服务器机柜占用情况  测试 资产1 中=2  资产2 后=1
  //$itemlist = $Ipdb_items->where('rackid = "'.$rackid.'" and id not in('.$objectid.')')->select();
  for($j=0;$j<count($itemlist);$j++){
    $other_rackposition=$itemlist[$j]['rackposition'];
    //预留状态颜色判断

    if ($itemlist[$j]['appointment']==1){$color='#FFD700';}else{$color='#ff9999';}

        if ($itemlist[$j]['rackposdepth']==1){  //后

          if ($i>=$itemlist[$j]['rackposition'] and $i<$itemlist[$j]['lastpos']){

            //排除同一U位
            if ($i>=$rackposition and $i<$lastpos){ //同一U位不显示


            }else{
                $racktable.='<tr align="middle" >';
                $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
                $racktable.='<td class="emptyrow" ></td>';
                $racktable.='<td class="emptyrow" ></td>';
                //$startrackpostion=($rackposition+$usize)-1;
                $tt_start = $itemlist[$j]['rackposition']+$itemlist[$j]['usize']-1;
                if($i==$tt_start){
                  $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'">'.$itemlist[$j]['common_name'].'</td>'; 
                }
                $racktable.='</tr>';

            }

          }

        }elseif($itemlist[$j]['rackposdepth']==2){ //中
            
          if ($i>=$itemlist[$j]['rackposition'] and $i<$itemlist[$j]['lastpos']){

            //排除同一U位
            if ($i>=$rackposition and $i<$lastpos){ //同一U位不显示


            }else{
                $racktable.='<tr align="middle" >';
                $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
                $racktable.='<td class="emptyrow" ></td>';
                $tt_start = $itemlist[$j]['rackposition']+$itemlist[$j]['usize']-1;
                if($i==$tt_start){
                  $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'">'.$itemlist[$j]['common_name'].'222</td>'; 
                }
                $racktable.='<td class="emptyrow" ></td>';
                $racktable.='</tr>';

            }

          }

        }elseif($itemlist[$j]['rackposdepth']==3){ //中后
            
          if ($i>=$itemlist[$j]['rackposition'] and $i<$itemlist[$j]['lastpos']){
            //排除同一U位
            if ($i>=$rackposition and $i<$lastpos){ //同一U位不显示


            }else{

                $racktable.='<tr align="middle" >';
                $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
                $racktable.='<td class="emptyrow" ></td>';
                $tt_start = $itemlist[$j]['rackposition']+$itemlist[$j]['usize']-1;
                if($i==$tt_start){
                  $racktable.='<td class="emptyrow" colspan="2" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'">'.$itemlist[$j]['common_name'].'333</td>'; 
                }
                $racktable.='</tr>';
            }

          }

        }elseif($itemlist[$j]['rackposdepth']==4){ //前
            
          if ($i>=$itemlist[$j]['rackposition'] and $i<$itemlist[$j]['lastpos']){
                        //排除同一U位
            if ($i>=$rackposition and $i<$lastpos){ //同一U位不显示


            }else{

                $racktable.='<tr align="middle" >';
                $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
                //不是同一U位判断
                $tt_start = $itemlist[$j]['rackposition']+$itemlist[$j]['usize']-1;
                if($i==$tt_start){
                    $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'">'.$itemlist[$j]['common_name'].'444</td>'; 
                }
                $racktable.='<td class="emptyrow" ></td>';
                $racktable.='<td class="emptyrow" ></td>';
                $racktable.='</tr>';

                // $racktable.='<tr align="middle" >';
                // $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
                // //不是同一U位判断

                // if ($i==$itemlist[$j]['rackposition']){
                //     $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'">'.$itemlist[$j]['common_name'].'444</td>'; 
                // }

                // $racktable.='<td class="emptyrow" ></td>';
                // $racktable.='<td class="emptyrow" ></td>';
                // $racktable.='</tr>';
            }




          }

        }elseif($itemlist[$j]['rackposdepth']==5){ //前中
            
          if ($i>=$itemlist[$j]['rackposition'] and $i<$itemlist[$j]['lastpos']){
                        //排除同一U位
            if ($i>=$rackposition and $i<$lastpos){ //同一U位不显示


            }else{

                $racktable.='<tr align="middle" >';
                $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
                $tt_start = $itemlist[$j]['rackposition']+$itemlist[$j]['usize']-1;
                if($i==$tt_start){
                  $racktable.='<td class="emptyrow" colspan="2" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'">'.$itemlist[$j]['common_name'].'</td>'; 
                }else{

                }
                $racktable.='<td class="emptyrow" ></td>';
                $racktable.='</tr>';
            }

          }

        }elseif($itemlist[$j]['rackposdepth']==6){ //前中后
            
          if ($i>=$itemlist[$j]['rackposition'] and $i<$itemlist[$j]['lastpos']){
                $racktable.='<tr align="middle" >';
                $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
                $tt_start = $itemlist[$j]['rackposition']+$itemlist[$j]['usize']-1;
                if($i==$tt_start){
                  $racktable.='<td class="emptyrow" colspan="3" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'">'.$itemlist[$j]['common_name'].'</td>'; 
                }
                $racktable.='</tr>';

          }

    }



  }




//-----------------------------未占用部分

  if (in_array($i, $temp)){
  }else{
      $racktable.='<tr align="middle" >';
      $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
      $racktable.='<td class="emptyrow" ></td>';
      $racktable.='<td class="emptyrow" ></td>';
      $racktable.='<td class="emptyrow" ></td>'; 
      $racktable.='</tr>';  
  }



  }


  return $racktable;

}







public function  showrackposdepth($rackid,$objectid){

  $Ipdb_items =M("ipdb_items");
  $Ipdb_racks =M("ipdb_racks");

  //empty($objectid) && $this->error('请输入ID');
  //empty($rackid) && $this->error('请输入机柜ID');

  if (!empty($objectid)){
      $olist = $Ipdb_items->where('id = "'.$objectid.'"')->select();
      $common_name=$olist[0]['common_name'];
  }


  $racklist = $Ipdb_racks->where('id = "'.$rackid.'"')->select();
  $usizecount=$racklist[0]['usize'];

  $racktable='';
  $lastpos=$rackposition+$usize;

  $itemlist = $Ipdb_items->where('rackid = "'.$rackid.'" ')->select(); //and id not in('.$objectid.')
  for($j=0;$j<count($itemlist);$j++){
      $itemlist[$j]['lastpos']=$itemlist[$j]['usize']+$itemlist[$j]['rackposition'];
  }    


  $allitemlist = $Ipdb_items->where('rackid = "'.$rackid.'" ')->select();
  $ids='';
  for($j=0;$j<count($allitemlist);$j++){
      $Tempusize=$allitemlist[$j]['usize'];
      $Temprackposition=$allitemlist[$j]['rackposition'];
      if ($Tempusize==1){$ids=$ids.','.$Temprackposition;}//1U 
      if ($Tempusize>1){//多U
          $Templastpos=$Tempusize+$Temprackposition;
          for($jj=$Temprackposition;$jj<$Templastpos;$jj++){
          //for($jj=$Templastpos-1;$jj>=$Temprackposition;$jj--){
              $ids=$ids.','.$jj;
          }
      } 

  }
  // for($jj=$Templastpos-1;$jj>=$Temprackposition;$jj--){
  $ids=substr($ids,1,strlen($ids));//占用的u位起始和结束

  // unset($temp);
  $temp= explode(',',$ids);
  for($ii=0;$ii<count($temp);$ii++){
    $temp[$ii] = intval($temp[$ii]);
  }
  
  //var_dump($temp); //ok
  

   //for($i=1;$i<=$usizecount;$i++){
   //for($i=$usizecount;$i>=1;$i--)
  for($i=$usizecount;$i>=1;$i--){
    
//-------------------------------------当前服务器机柜占用情况

    //预留状态颜色判断
    if ($appointment==1){$color='#FFD700';}else{$color='#00FFFF';}

    if ($rackposdepth==1 ){  //后

      if ($i>=$rackposition and $i<$lastpos){
            $racktable.='<tr align="middle" >';
            $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
            $racktable.='<td class="emptyrow" ></td>';
            $racktable.='<td class="emptyrow" ></td>';
            $startrackpostion=($rackposition+$usize)-1;
            if($i==$startrackpostion){ //正序用 $rackposition  降序用=startrackpostion
                $url = U('Item/item_info',array('id'=>$objectid));
                $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$usize.'" style="background-color:'.$color.'"><a href="'.$url.'">'.$common_name.'</a></td>'; 
            }
            //$racktable.= $label;
            $racktable.='</tr>'; 
      }

    }elseif($rackposdepth==2){ //中

      if ($i>=$rackposition and $i<$lastpos){
            $racktable.='<tr align="middle" >';
            $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
            $racktable.='<td class="emptyrow" ></td>';
            $startrackpostion=($rackposition+$usize)-1;
            if($i==$startrackpostion){ //正序用 $rackposition  降序用=startrackpostion
                $url = U('Item/item_info',array('id'=>$objectid));
              $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$usize.'" style="background-color:'.$color.'"><a href="'.$url.'">'.$common_name.'</a></td>'; 
            }
            $racktable.='<td class="emptyrow" ></td>';
            //$racktable.= $label;
            $racktable.='</tr>'; 
      }


    }elseif($rackposdepth==3){ //中后
        
      if ($i>=$rackposition and $i<$lastpos){
            $racktable.='<tr align="middle" >';
            $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
            $racktable.='<td class="emptyrow" ></td>';
            $startrackpostion=($rackposition+$usize)-1;
            if($i==$startrackpostion){ //正序用 $rackposition  降序用=startrackpostion
                $url = U('Item/item_info',array('id'=>$objectid));
              $racktable.='<td class="emptyrow" colspan="2" rowspan="'.$usize.'" style="background-color:'.$color.'"><a href="'.$url.'">'.$common_name.'</a></td>'; 
            }
            $racktable.='</tr>';

      }

    }elseif($rackposdepth==4){ //前
        
      if ($i>=$rackposition and $i<$lastpos){
            $racktable.='<tr align="middle" >';
            $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
            $startrackpostion=($rackposition+$usize)-1;
            if($i==$startrackpostion){ //正序用 $rackposition  降序用=startrackpostion
                $url = U('Item/item_info',array('id'=>$objectid));
                $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$usize.'" style="background-color:'.$color.'"><a href="'.$url.'">'.$common_name.'</a></td>'; 
            }
            $racktable.='<td class="emptyrow" ></td>';
            $racktable.='<td class="emptyrow" ></td>';
            $racktable.='</tr>';

      }

    }elseif($rackposdepth==5){ //前中
        
      if ($i>=$rackposition and $i<$lastpos){
            $racktable.='<tr align="middle" >';
            $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
            $startrackpostion=($rackposition+$usize)-1;
            if($i==$startrackpostion){ //正序用 $rackposition  降序用=startrackpostion
                $url = U('Item/item_info',array('id'=>$objectid));
                $racktable.='<td class="emptyrow" colspan="2" rowspan="'.$usize.'" style="background-color:'.$color.'"><a href="'.$url.'">'.$common_name.'</a></td>'; 
            }
            $racktable.='<td class="emptyrow" ></td>';
            $racktable.='</tr>';

      }

    }elseif($rackposdepth==6){ //前中后

      if ($i>=$rackposition and $i<$lastpos){
            $racktable.='<tr align="middle" >';
            $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
            $startrackpostion=($rackposition+$usize)-1;
            if($i==$startrackpostion){ //正序用 $rackposition  降序用=startrackpostion
            //if($i==$rackposition){
                $url = U('Item/item_info',array('id'=>$objectid));
              $racktable.='<td class="emptyrow" colspan="3" rowspan="'.$usize.'" style="background-color:'.$color.'"><a href="'.$url.'">'.$common_name.'</a></td>'; 
            }
            $racktable.='</tr>';

      }

    }

//-------------------------------------其他服务器机柜占用情况  测试 资产1 中=2  资产2 后=1
  //$itemlist = $Ipdb_items->where('rackid = "'.$rackid.'" and id not in('.$objectid.')')->select();
  for($j=0;$j<count($itemlist);$j++){
  //for($j=count($itemlist);$j>0;$j--){
    $other_rackposition=$itemlist[$j]['rackposition'];
    //预留状态颜色判断
    if ($itemlist[$j]['appointment']==1){$color='#FFD700';}else{$color='#ff9999';}
    if ($itemlist[$j]['id']==$objectid){$color='#00FFFF';}else{$color='#ff9999';}


        if ($itemlist[$j]['rackposdepth']==1){  //后

          if ($i>=$itemlist[$j]['rackposition'] and $i<$itemlist[$j]['lastpos']){

            //排除同一U位
            if ($i>=$rackposition and $i<$lastpos){ //同一U位不显示


            }else{
                $racktable.='<tr align="middle" >';
                $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
                $racktable.='<td class="emptyrow" ></td>';
                $racktable.='<td class="emptyrow" ></td>';

                $startrackpostion=($itemlist[$j]['rackposition']+$itemlist[$j]['usize'])-1;
                if($i==$startrackpostion){ //正序用 $itemlist[$j]['rackposition']  降序用=startrackpostion
                    $url = U('Item/item_info',array('id'=>$itemlist[$j]['id']));
                    $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'"><a href="'.$url.'">'.$itemlist[$j]['common_name'].'</a></td>'; 
                }
                $racktable.='</tr>';

            }

          }


        }elseif($itemlist[$j]['rackposdepth']==2){ //中
            
          if ($i>=$itemlist[$j]['rackposition'] and $i<$itemlist[$j]['lastpos']){

            //排除同一U位
            if ($i>=$rackposition and $i<$lastpos){ //同一U位不显示


            }else{
                $racktable.='<tr align="middle" >';
                $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
                $racktable.='<td class="emptyrow" ></td>';
                $startrackpostion=($itemlist[$j]['rackposition']+$itemlist[$j]['usize'])-1;
                if($i==$startrackpostion){ //正序用 $itemlist[$j]['rackposition']  降序用=startrackpostion
                    $url = U('Item/item_info',array('id'=>$itemlist[$j]['id']));
                    $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'"><a href="'.$url.'">'.$itemlist[$j]['common_name'].'</a></td>'; 
                }
                $racktable.='<td class="emptyrow" ></td>';
                $racktable.='</tr>';

            }

          }

        }elseif($itemlist[$j]['rackposdepth']==3){ //中后
            
          if ($i>=$itemlist[$j]['rackposition'] and $i<$itemlist[$j]['lastpos']){
            //排除同一U位
            if ($i>=$rackposition and $i<$lastpos){ //同一U位不显示


            }else{

                $racktable.='<tr align="middle" >';
                $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
                $racktable.='<td class="emptyrow" ></td>';
                $startrackpostion=($itemlist[$j]['rackposition']+$itemlist[$j]['usize'])-1;
                if($i==$startrackpostion){ //正序用 $itemlist[$j]['rackposition']  降序用=startrackpostion
                    $url = U('Item/item_info',array('id'=>$itemlist[$j]['id']));
                    $racktable.='<td class="emptyrow" colspan="2" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'"><a href="'.$url.'">'.$itemlist[$j]['common_name'].'</a></td>'; 
                }
                $racktable.='</tr>';
            }

          }

        }elseif($itemlist[$j]['rackposdepth']==4){ //前
            
          if ($i>=$itemlist[$j]['rackposition'] and $i<$itemlist[$j]['lastpos']){
                        //排除同一U位
            if ($i>=$rackposition and $i<$lastpos){ //同一U位不显示


            }else{

                $racktable.='<tr align="middle" >';
                $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
                //不是同一U位判断
                $startrackpostion=($itemlist[$j]['rackposition']+$itemlist[$j]['usize'])-1;
                if($i==$startrackpostion){ //正序用 $itemlist[$j]['rackposition']  降序用=startrackpostion
                    $url = U('Item/item_info',array('id'=>$itemlist[$j]['id']));
                    $racktable.='<td class="emptyrow" colspan="1" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'"><a href="'.$url.'">'.$itemlist[$j]['common_name'].'</a></td>'; 
                }
                $racktable.='<td class="emptyrow" ></td>';
                $racktable.='<td class="emptyrow" ></td>';
                $racktable.='</tr>';

            }




          }

        }elseif($itemlist[$j]['rackposdepth']==5){ //前中
            
          if ($i>=$itemlist[$j]['rackposition'] and $i<$itemlist[$j]['lastpos']){
                        //排除同一U位
            if ($i>=$rackposition and $i<$lastpos){ //同一U位不显示


            }else{

                $racktable.='<tr align="middle" >';
                $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';

                $startrackpostion=($itemlist[$j]['rackposition']+$itemlist[$j]['usize'])-1;
                if($i==$startrackpostion){ //正序用 $itemlist[$j]['rackposition']  降序用=startrackpostion
                    $url = U('Item/item_info',array('id'=>$itemlist[$j]['id']));
                    $racktable.='<td class="emptyrow" colspan="2" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'"><a href="'.$url.'">'.$itemlist[$j]['common_name'].'</a></td>'; 
                }else{

                }
                $racktable.='<td class="emptyrow" ></td>';
                $racktable.='</tr>';
            }

          }

        }elseif($itemlist[$j]['rackposdepth']==6){ //前中后
          if ($i>=$itemlist[$j]['rackposition'] and $i<$itemlist[$j]['lastpos']){
                $racktable.='<tr align="middle" >';
                $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
                //echo $i.'=='.$itemlist[$j]['rackposition']."<br />";
                $startrackpostion=($itemlist[$j]['rackposition']+$itemlist[$j]['usize'])-1;
                if($i==$startrackpostion){ //正序用 $itemlist[$j]['rackposition']  降序用=startrackpostion
                    $url = U('Item/item_info',array('id'=>$itemlist[$j]['id']));
                    $racktable.='<td class="emptyrow" colspan="3" rowspan="'.$itemlist[$j]['usize'].'" style="background-color:'.$color.'"><a href="'.$url.'">'.$itemlist[$j]['common_name'].'</a></td>'; 
                }

                $racktable.='</tr>';

          }

    }



  }




//-----------------------------未占用部分

  if (in_array($i, $temp)){
  }else{
      $racktable.='<tr align="middle" >';
      $racktable.='<td class="checkinput" style="background-color:white">'.$i.'</td>';
      $racktable.='<td class="emptyrow" ></td>';
      $racktable.='<td class="emptyrow" ></td>';
      $racktable.='<td class="emptyrow" ></td>'; 
      $racktable.='</tr>';  
  }



  }

  return $racktable;

}










public function  putrackslog($rackid,$objectid,$flag,$movearray){

   if(!is_login()){
     $this->redirect("User/login");
   }    

   $uid        =   is_login();

  empty($objectid) && $this->error('请输入设备参数ID');
  empty($rackid) && $this->error('请输入机柜参数ID');

  $Ipdb_racks =M("ipdb_racks");
  $Ipdb_rackslog =M("ipdb_rackslog");
  $Ipdb_items =M("ipdb_items");
  $Ipdb_locations =M("ipdb_locations");
  $Ipdb_locareas =M("ipdb_locareas");

  $racklist=$Ipdb_racks->where(' id="'.$rackid.'" ')->order('id asc')->select();
  $locareaid=$racklist[0]['locareaid'];
  $rackname=$racklist[0]['name'];
  $rackcomments=$racklist[0]['comments'];
  $locationid=$racklist[0]['locationid'];

  $loclist=$Ipdb_locations->where(' id="'.$locationid.'" ')->order('id asc')->select();
  $locname=$loclist[0]['name'];
  $floor=$loclist[0]['floor'];

  $arealist=$Ipdb_locareas->where(' id="'.$locareaid.'" ')->order('id asc')->select();
  $areaname=$arealist[0]['areaname'];

  $itemlist=$Ipdb_items->where(' id="'.$objectid.'" ')->order('id asc')->select();
  $item_label=$itemlist[0]['label'];
  $item_rackposition=$itemlist[0]['rackposition'];
  $item_usize=$itemlist[0]['usize'];
  $item_rackid=$itemlist[0]['rackid'];
  $item_locationid=$itemlist[0]['locationid'];
  $item_locareaid=$itemlist[0]['locareaid'];



  if($flag=='up'){$status='上架';}elseif($flag=='down'){$status='报废';}elseif($flag=='move'){$status='迁移';}elseif($flag=='obligate'){$status='预留';}elseif($flag=='obligateok'){$status='预留转上架';}
  $meta.="状态：".$status."<br />";

  $time=date("Y-m-d H:i:s",time());

if($flag=='up'){
    if (empty($item_rackposition)){
        $item_rackposition= $_REQUEST['rackposition'];
    }
    $meta.="时间：".$time."<br />";
    $meta.="设备：".$item_label."<br />";
    $meta.="数据中心/房间/机柜：".$locname.'/'.$areaname.'/'.$rackname."<br />";
    $meta.="U尺寸/起始位置：".$item_usize.'U/'.$item_rackposition."U<br />";
}elseif($flag=='down'){
    $meta.="报废时间：".$time."<br />";
    $meta.="设备：".$item_label."<br />";
    $meta.="数据中心/房间/机柜：".$locname.'/'.$areaname.'/'.$rackname."<br />";
    $meta.="U尺寸/起始位置：".$item_usize.'U/'.$item_rackposition."U<br />";
}elseif($flag=='move'){

    $moveareaid=$movearray['moveareaid'];
    $movelocationid=$movearray['movelocationid'];
    $moverackposition=$movearray['moverackposition'];
    $movemyusize=$movearray['movemyusize'];
    $moverackid=$movearray['moverackid'];

    $moveracklist=$Ipdb_racks->where(' id="'.$moverackid.'" ')->order('id asc')->select();
    $movelocareaid=$moveracklist[0]['locareaid'];
    $moverackname=$moveracklist[0]['name'];
    $movelocationid=$moveracklist[0]['locationid'];

    $moveloclist=$Ipdb_locations->where(' id="'.$movelocationid.'" ')->order('id asc')->select();
    $movelocname=$moveloclist[0]['name'];
    $movefloor=$moveloclist[0]['floor'];

    $movearealist=$Ipdb_locareas->where(' id="'.$moveareaid.'" ')->order('id asc')->select();
    $moveareaname=$movearealist[0]['areaname'];

    $item_loclist=$Ipdb_locations->where(' id="'.$item_locationid.'" ')->order('id asc')->select();
    $locname=$item_loclist[0]['name'];
    $floor=$item_loclist[0]['floor'];

    $item_arealist=$Ipdb_locareas->where(' id="'.$item_locareaid.'" ')->order('id asc')->select();
    $areaname=$item_arealist[0]['areaname'];

    $item_racklist=$Ipdb_racks->where(' id="'.$item_rackid.'" ')->order('id asc')->select();
    $rackcomments=$item_racklist[0]['comments'];

    $meta.="时间：".$time."<br />";
    $meta.="设备：".$item_label."<br />";
    $meta.="从<br />数据中心/房间/机柜：".$locname.'/'.$areaname.'/'.$rackname."<br />";
    $meta.="U尺寸/起始位置：".$item_usize.'U/'.$item_rackposition."U<br />";  
    $meta.="到<br />数据中心/房间/机柜：".$movelocname.'/'.$moveareaname.'/'.$moverackname."<br />";
    $meta.="U尺寸/起始位置：".$movemyusize.'U/'.$moverackposition."U<br />";  

}else{
    $meta.="时间：".$time."<br />";
    $meta.="设备：".$item_label."<br />";
    $meta.="数据中心/房间/机柜：".$locname.'/'.$areaname.'/'.$rackname."<br />";
    $meta.="U尺寸/起始位置：".$item_usize.'U/'.$item_rackposition."U<br />";   
}


  $adddata['uid']=$uid;
  $adddata['eventclock']=time();
  $adddata['locationid']=$locationid;
  $adddata['locareaid']=$locareaid;
  $adddata['meta']=$meta;
  $adddata['rackid']=$rackid;
  $adddata['usize']=$item_usize;
  $adddata['rackposition']=$item_rackposition;
  $adddata['itemid']=$objectid;
  $adddata['flag']=$flag;

  $Ipdb_rackslog->add($adddata);
 

}





public function  rackmap(){

   if(!is_login()){
     $this->redirect("User/login");
   }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');


  $Ipdb_locations =M("ipdb_locations");
  $Ipdb_locareas =M("ipdb_locareas");
  $Ipdb_racks =M("ipdb_racks");

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




  if(empty($lids)){
    $list = $Ipdb_racks->where('locareaid<>0')->group('locareaid')->order('id desc')->select();
  }else{
    $list = $Ipdb_racks->where('locationid in ('.$lids.') ')->group('locareaid')->order('id desc')->select();
  }

  $showmenu=array();
  $tr='';
  for($i=0;$i<count($list);$i++){

    $id=$list[$i]['id'];
    $label=$list[$i]['label'];
    $locareaid=$list[$i]['locareaid'];
    $locationid=$list[$i]['locationid'];
    $llist=$Ipdb_locations->where('id = "'.$locationid.'" ')->order('id desc')->select();
    $area=$llist[0]['area'];
    $locname=$llist[0]['name'];
    $floor=$llist[0]['floor'];

    $arealist=$Ipdb_locareas->where('id = "'.$locareaid.'" ')->order('id desc')->select();

    $templocareaid=$arealist[0]['id'];
    $areaname=$arealist[0]['areaname'];
 
    unset($tr);
    unset($rlist);
    $rlist = $Ipdb_racks->where('locareaid = "'.$templocareaid.'" ')->order('id desc')->select();
    for($ii=0;$ii<count($rlist);$ii++){
        $rid=$rlist[$ii]['id'];
        //echo count($rlist).'----------<br />';
        $rname=$rlist[$ii]['name'];
        $tr.='<a href="'.__ROOT__.'/index.php/Home/Racks/viewrack/rackid/'.$rid.'.html" target="_blank">'.$rname.'</a>&nbsp;&nbsp;';

    }

    $list[$i]['rlist']=$tr;

    $list[$i]['locname']=$locname;
    $list[$i]['areaname']=$areaname;
    $list[$i]['floor']=$floor;
    $list[$i]['area']=$area;

  }


  $this->assign('url_flag','rackmap');
  $this->assign('json_showmenu',$json_showmenu);
  $this->assign('tr',$tr);
  $this->assign('list',$list);
  $this->assign('showlabel',$showlabel);
  $this->display();  
}






public function  noderack(){

   if(!is_login()){
     $this->redirect("User/login");
   }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');
  $Ipdb_locations =M("ipdb_locations");
  $Ipdb_locareas =M("ipdb_locareas");
  $Ipdb_racks =M("ipdb_racks");

  $rackid= $_REQUEST['rackid'];
  $location_id= $_REQUEST['location_id'];
  $lids=implode(',',$location_id);
  //-----------------------------获取动态机房，房间数据
  $showlabel='';
  $locationlist=$Ipdb_locations->group(' area ')->order('id asc')->select();

  for($i=0;$i<count($locationlist);$i++){
      $lid=$locationlist[$i]['id'];
      $distname=$locationlist[$i]['area'];
      if ($i>0){
          $showlabel.='<br />';
      }
      $showlabel.='<label><input type="checkbox"  class="0" value="" checked >';
      $showlabel.=$distname;
      $showlabel.='</label>';

      $sublocationlist=$Ipdb_locations->where(' area="'.$distname.'" ')->order('id asc')->select();
      for($ii=0;$ii<count($sublocationlist);$ii++){
        $sublid=$sublocationlist[$ii]['id'];
        $name=$sublocationlist[$ii]['name'];
        $showlabel.='<br />&nbsp;&nbsp;&nbsp;&nbsp;';
        $showlabel.='<label><input type="checkbox" name="location_id[]" class="0" value="'.$sublid.'" checked >';
        $showlabel.=$name;
        $showlabel.='</label>';
      }


  }


  if(empty($lids)){
    $list = $Ipdb_racks->order('id desc')->select();
  }else{
    $list = $Ipdb_racks->where('locationid in ('.$lids.') ')->order('id desc')->select();
  }

  $showmenu=array();
  for($i=0;$i<count($list);$i++){

    $id=$list[$i]['id'];
    $label=$list[$i]['label'];
    $locareaid=$list[$i]['locareaid'];
    $locationid=$list[$i]['locationid'];
    $llist=$Ipdb_locations->where('id = "'.$locationid.'" ')->order('id desc')->select();
    $area=$llist[0]['area'];
    $locname=$llist[0]['name'];
    $floor=$llist[0]['floor'];
    $arealist=$Ipdb_locareas->where('id = "'.$locareaid.'" ')->order('id desc')->select();
    $areaname=$arealist[0]['areaname'];

    $list[$i]['locname']=$locname;
    $list[$i]['areaname']=$areaname;
    $list[$i]['floor']=$floor;
    $list[$i]['area']=$area;

  }


  $this->assign('url_flag','noderack');
  $this->assign('json_showmenu',$json_showmenu);
  $this->assign('list',$list);
  $this->assign('showlabel',$showlabel);
  $this->display();  
}



  public function viewrack(){
 
     if(!is_login()){
       $this->redirect("User/login");
     }    

    $uid        =   is_login();

    $Ipdb_racks =M("ipdb_racks");
    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    $Department =M("department");
    $Ipdb_items =M("ipdb_items");

    $id=$_REQUEST['rackid'];
    empty($id) && $this->error('请输入id');

//---------------------------------------------------------------show 机柜信息
    
    $racktable=$this->showrackposdepth($id);

//------------------------------------------------------------------机柜信息结束

    $racklist=$Ipdb_racks->where('id="'.$id.'" ')->order('id desc')->select();
    $locationid=$racklist[0]['locationid'];
    $locareaid=$racklist[0]['locareaid'];
    $usize=$racklist[0]['usize'];
    $power=$racklist[0]['power'];
    $rackname=$racklist[0]['name'];
    $comments=$racklist[0]['comments'];
    
    $lease_mode=$racklist[0]['lease_mode'];
    if (empty($lease_mode)){$lease_mode='散U租用';}else{$lease_mode='整柜租用';}
    $departmentid=$racklist[0]['departmentid'];

    $deplist=$Department->where('id="'.$departmentid.'" ')->order('id desc')->select();
    $dname=$deplist[0]['depart_name'];


    $locationlist=$Ipdb_locations->where('id="'.$locationid.'" ')->order('id desc')->select();
    $lid=$locationlist[0]['id']; 
    $name=$locationlist[0]['name']; 
    $floor=$locationlist[0]['floor'];//."层"
    $area=$locationlist[0]['area'];
    //判断是否是库房类型
    $is_store = $locationlist[0]['is_store'];
    

    $itemcount=$Ipdb_items->where('rackid="'.$id.'"')->count();
    $roomcount=$Ipdb_locareas->where('locationid="'.$lid.'"')->count();
    $arealist=$Ipdb_locareas->where('id="'.$locareaid.'"')->select(); 
    $areaname=$arealist[0]['areaname']; 
//---------------------------------机柜设备占用率
    $itemlist=$Ipdb_items->where('rackid = '.$id.' ')->field(' sum(usize) as usedusize')->select();    
    $usedusize=$itemlist[0]['usedusize'];
    $this->assign('usedusize_u',$usedusize);
    $usedusize=($usedusize/$usize)*100;
    $usedusize=round($usedusize);
    
    //机柜下属所有设备
    $itemlist_list=$Ipdb_items->where('rackid = '.$id.' ')->select();

    $this->assign('id',$id);
    $this->assign('racktable',$racktable);
    $this->assign('departmentid',$departmentid);
    $this->assign('usize',$usize);
    $this->assign('name',$name);
    $this->assign('dname',$dname);
    $this->assign('power',$power);
    $this->assign('area',$area);
    $this->assign('areaname',$areaname);
    $this->assign('lease_mode',$lease_mode);
    $this->assign('rackname',$rackname);
    $this->assign('comments',$comments);
    $this->assign('floor',$floor);
    $this->assign('itemcount',$itemcount);
    $this->assign('usedusize',$usedusize);
    $this->assign('itemlist',$itemlist_list);
    $this->assign('is_store',$is_store);
    $this->assign('url_flag','listracks');
    $this->display();   
  } 




  public function itemnew(){
 
     if(!is_login()){
       $this->redirect("User/login");
     }    

    $uid        =   is_login();
    $nickname = M('user')->getFieldByUid($uid, 'username');

    $this->display();   
  } 





public function  multisearch(){

   if(!is_login()){
     $this->redirect("User/login");
   }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');

  $Ipdb_treearea =M("ipdb_treearea");
  $Ipdb_tree =M("ipdb_tree");
  //别名
  $treelist=$Ipdb_tree->order(' id asc')->select();
  

  //$table.='<label for="device-type" class="col-xs-16 control-label">'.$another_name.'</label>';
  //$table.='<div class="col-xs-16">';
  // $table.='<label class="radio-inline">';
  // $table.='<input type="checkbox" id="device-type" name="searchbox[]" value="'.$id.'">';
  // $table.=$name;
  // $table.='</label>';
  //$table.='</div>';


  $list=$Ipdb_treearea->where('contypeid=0')->order(' id asc')->select();
  $table='';
  $checkall_nodes='';
  for($i=0;$i<count($list);$i++){
      $id=$list[$i]['id'];
      $sublist=$Ipdb_treearea->where('contypeid ="'.$id.'" ')->order(' id asc')->select();
      for($ii=0;$ii<count($sublist);$ii++){
          $subid=$sublist[$ii]['id'];
          $subids=$subids.','.$subid;
          $threelist=$Ipdb_treearea->where('contypeid ="'.$subid.'" ')->order(' id asc')->select();
          for($iii=0;$iii<count($threelist);$iii++){
              $threeid=$threelist[$iii]['id'];
              $threeids=$threeids.','.$threeid;
              $fourlist=$Ipdb_treearea->where('contypeid ="'.$threeid.'" ')->order(' id asc')->select();
              for($iiii=0;$iiii<count($fourlist);$iiii++){
                  $fourid=$fourlist[$iiii]['id'];
                  $fourids=$fourids.','.$fourid;
              }
          }

      }
      $ids=$ids.','.$list[$i]['id'];
  }
  $ids=substr($ids,1,strlen($ids));
  $subids=substr($subids,1,strlen($subids));
  $threeids=substr($threeids,1,strlen($threeids));
  $fourids=substr($fourids,1,strlen($fourids));



  //$list=$Ipdb_treearea->where('id in('.$ids.')')->order(' id asc')->select();




  $url=$this->checkurl();
  $this->assign('url',$url); 
  $this->assign('table',$table);
  $this->display();  


}





  public function viewmeta(){

    if(!is_login()){
      $this->redirect("User/login");
    } 

    $uid        =   is_login();
    $nickname = M('user')->getFieldByUid($uid, 'username');

    $id=$_REQUEST['id'];
    empty($id) && $this->error('请输入id');
    $Ipdb_rackslog = M('ipdb_rackslog');
    $list=$Ipdb_rackslog->where('id="'.$id.'"')->order(' id asc')->select();
    $meta=$list[0]['meta'];

    $this->assign('uid',$uid);
    $this->assign('nickname',$nickname);
    $this->assign('meta',$meta);
    $this->assign('id',$id);
    $this->display();
  }








//----------------------------------------------------------上 下 迁移 预留-----------------------------------------------------------------



public function  rackspace_up(){

   if(!is_login()){
     $this->redirect("User/login");
   }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');

  $Ipdb_items =M("ipdb_items");
  $Department =M("department");
  $Itemtype =M("itemtype");
  $Ipdb_racks =M("ipdb_racks");
  $Ipdb_locations =M("ipdb_locations");
  $Ipdb_locareas =M("ipdb_locareas");



  $objectid= $_REQUEST['objectid'];
  $ajax= $_REQUEST['ajax'];
  $flag= $_REQUEST['flag'];
  empty($objectid) && $this->error('请输入id');
  
  $block= $_REQUEST['block'];
  if ($flag=='up1'){
      $block='';
  }


  if ($flag=='obligateok'){
    $okdata['appointment']=0;
    $Ipdb_items->where('id="'.$objectid.'"')->setField($okdata);
    $this->show('<script type="text/javascript" >alert("设备预留变更为上架成功");window.location.href="{:U(\"Racks/rackspace?objectid='.$objectid.'\")}"; </script>');
    exit();
  }
    



  $list = $Ipdb_items->where('id = "'.$objectid.'"')->select();
  $common_name=$list[0]['common_name'];
  $label=$list[0]['label'];
  $ip=$list[0]['ip'];
  $sn=$list[0]['sn'];
  $comments=$list[0]['comments'];
  $usize=$list[0]['usize'];
  $rackid=$list[0]['rackid'];
  $rackposition=$list[0]['rackposition'];
  $system=$list[0]['system'];
  $model=$list[0]['model'];
  $rackposdepth=$list[0]['rackposdepth'];
  $manufacturerid=$list[0]['manufacturerid'];
  $locareaid=$list[0]['locareaid'];
  $locationid=$list[0]['locationid'];
  $appointment=$list[0]['appointment'];

  if ($appointment==1){$status='<font color="#FFD700">预留</font> &nbsp;&nbsp;>> &nbsp;&nbsp;<a href="'.__ROOT__.'/index.php/Home/Racks/rackspace/flag/obligateok/objectid/'.$objectid.'.html">变更上架</a>';}
  if (empty($locationid) && empty($locareaid) && empty($rackid)){$status='<font color="">下架</font>';}
  if (!empty($locationid) && empty($appointment) && !empty($locareaid) && !empty($rackid)){$status='<font color="#00FFFF">上架</font>';}



  $itemtypeid=$list[0]['itemtypeid'];
  $typelist = $Itemtype->where('id = "'.$itemtypeid.'"')->select();
  $typedesc=$typelist[0]['typedesc'];

  $departmentid=$list[0]['departmentid'];
  $dlist = $Department->where('id = "'.$departmentid.'"')->select();
  $dname=$dlist[0]['name'];

  $racklist = $Ipdb_racks->where('id = "'.$rackid.'"')->select();
  $usizecount=$racklist[0]['usize'];
  $revnums=$racklist[0]['revnums'];
  $rackcomments=$racklist[0]['comments'];

  $locationlist = $Ipdb_locations->where('id = "'.$locationid.'"')->select();
  $location_name=$locationlist[0]['name'];
  $locarealist = $Ipdb_locareas->where('id = "'.$locareaid.'"')->select();
  $areaname=$locarealist[0]['areaname'];



  $racktable=$this->showeditrackposdepth($rackid,$objectid); 



  if(empty($usizecount)){
      $usizelist="";
      for($i=1;$i<51;$i++){
          $usizelist.="<option  value='".$i."'>".$i."U</option>";
      }

      $itemusizeoption=$usizelist;


  }else{
      //---------------------------------U位选择
        $usizelist="";
        for($i=1;$i<=$usizecount;$i++){
          if ($i==$rackposition){
            $usizelist.="<option  value='".$i."' selected='selected'>".$i."U</option>";
          }else{
            $usizelist.="<option  value='".$i."'>".$i."U</option>";
          }
        }  

      //---------------------------------机柜位置选择
      $itemusizeoption="";
      for($i=1;$i<=$usizecount;$i++){
        if ($i==$usize){
          $itemusizeoption.="<option  value='".$i."' selected='selected'>".$i."U</option>";
        }else{
          $itemusizeoption.="<option  value='".$i."'>".$i."U</option>";
        }
      }

  }


//---------------------------------机柜选择
  $racklist = $Ipdb_racks->select();
  $option='';
  $racklist=$Ipdb_racks->order('id asc')->select();
  $rackoption= '<option value ="" >请选择机柜...</option>';
  for($i=0;$i<count($racklist);$i++){      
      $depid=$racklist[$i]['id'];
      $tempcomments=$racklist[$i]['comments'];
      if ($depid==$rackid){
        $rackoption.='<option value ="'.$depid.'" selected="selected">'.$tempcomments.'</option>';
      }else{
        $rackoption.='<option value ="'.$depid.'" >'.$tempcomments.'</option>';
      }
      
  }


  if(IS_POST){
      $flag= $_REQUEST['flag'];//设备id

    if ($flag=='up'){
        $objectid= $_REQUEST['objectid'];//设备id
        $rackposdepthid= $_REQUEST['rackposdepthid'];//机柜深度id
        $rackposition= $_REQUEST['rackposition'];//设备起始U位
        $myusize= $_REQUEST['myusize'];//设备U大小
        $locationid= $_REQUEST['locationid'];//机房id
        $areaid= $_REQUEST['areaid'];//房间ID
        $rackid= $_REQUEST['rackid'];//机柜ID

        empty($objectid) && $this->error('请输入设备ID!');
        empty($rackposdepthid) && $this->error('请输入机柜深度!');
        empty($rackposition) && $this->error('请输入设备起始U位!');
        empty($myusize) && $this->error('请输入设备U大小!');
        empty($locationid) && $this->error('请输入机房ID!');
        empty($areaid) && $this->error('请输入房间ID!');
        empty($rackid) && $this->error('请输入机柜ID!');

//-----------------------------------判断U位是否占用 start----------------------------------------------

//--------------------获取当前设备要占U位id开始

        $mystart=$rackposition;
        if ($myusize==1){
          $myend=($rackposition+$myusize);
        }else{
          $myend=($rackposition+$myusize)-1;
        }
        //$mycount=$myend-$mystart;
        $my_alls='';
        for($j=0;$j<$myusize;$j++){ 
           $check_myrackposition=$mystart+$j;
           $my_alls=$my_alls.','.$check_myrackposition;
        }
        $my_alls=substr($my_alls,1,strlen($my_alls));
//--------------------获取当前设备要占U位id结束
        //echo $my_alls."------------<br />";


        $checkitemlist = $Ipdb_items->where('rackid = "'.$rackid.'" ')->select();
        $other_alls='';
        for($i=0;$i<count($checkitemlist);$i++){      
            $other_usize=$checkitemlist[$i]['usize'];
            $other_start=$checkitemlist[$i]['rackposition'];
            $other_end=($other_start+$other_usize)-1;

            $other_all='';
            for($ii=0;$ii<$other_usize;$ii++){ //2-5 共4U
                $check_otherrackposition=$other_start+$ii;
                $other_all=$other_all.','.$check_otherrackposition;
            }
            $other_all=substr($other_all,1,strlen($other_all));
            $other_alls=$other_alls.','.$other_all;
        }
        $other_alls=substr($other_alls,1,strlen($other_alls));

        //echo $other_alls."------------<br />";
        $my_allarrays=explode(',',$my_alls); 
        $other_allarrays=explode(',',$other_alls); 

        $my=array_intersect($my_allarrays,$other_allarrays);
        //var_dump($my);
        if (!empty($my)){
            $this->show('<script type="text/javascript" >alert("机柜U位已经被占用，请重新选择!");history.go(-1)</script>');
            exit();
        }
//-----------------------------------判断U位是否占用 end-------------------------------------------------






//-----------------------------------log start--------------------------------------------
        $this->putrackslog($rackid,$objectid,$flag);
//-----------------------------------log end----------------------------------------------

        unset($adddata);
        $adddata['rackposdepth']=I('post.rackposdepthid');
        $adddata['usize']=I('post.myusize');
        $adddata['rackposition']=I('post.rackposition');
        $adddata['rackid']=I('post.rackid');
        $adddata['locationid']=$locationid;
        $adddata['rackid']=$rackid;
        $adddata['locareaid']=$areaid;
        $adddata['appointment']=0; //1=预留 0是正常

        $Ipdb_items->where('id="'.$objectid.'"')->setField($adddata);
        $this->show('<script type="text/javascript" >alert("设备上架成功");window.location.href="{:U(\"Racks/rackspace_up?ajax=ok&block=block&objectid='.$objectid.'\")}"; </script>');
        exit();

    }elseif ($flag=='up1'){

        $objectid= $_REQUEST['objectid'];//设备id
        $myusize= $_REQUEST['myusize'];//设备U大小
        $movelocationid= $_REQUEST['locationid'];//机房id
        $moveareaid= $_REQUEST['areaid'];//房间ID
        $moverackid= $_REQUEST['rackid'];//机柜ID

        empty($objectid) && $this->error('请输入设备ID!');
        empty($myusize) && $this->error('请输入设备U大小!');
        empty($movelocationid) && $this->error('请输入机房ID!');
        empty($moveareaid) && $this->error('请输入房间ID!');
        empty($moverackid) && $this->error('请输入机柜ID!');

        $mlocarealist = $Ipdb_locareas->where('id = "'.$moveareaid.'"')->select();
        $moveareaname=$mlocarealist[0]['areaname'];

        $mracklist = $Ipdb_racks->where('id = "'.$moverackid.'"')->select();
        $movecomments=$mracklist[0]['comments'];

        $mlocationlist = $Ipdb_locations->where('id = "'.$movelocationid.'"')->select();
        $movelocationname=$mlocationlist[0]['name'];

//---------------------------------------------------------------show 机柜信息
    
        $moveracktable=$this->showrackposdepth($moverackid,$objectid);

//------------------------------------------------------------------机柜信息结束        



    }





  }


//-----------------------------------------------一二级联动
  $loclist=$Ipdb_locations->select();
  $str_= '<option value ="" >请选择数据中心...</option>';
  for($i=0;$i<count($loclist);$i++){      
      $id=$loclist[$i]['id'];
      $locname=$loclist[$i]['name'];
      $str_.='<option value ="'.$id.'" >'.$locname.'</option>';
  }
  $list2=$Ipdb_locareas->field('id,areaname,locationid')->select();
  $jsonlist2 = json_encode($list2);

  $list3=$Ipdb_racks->field('id,locationid,locareaid,name')->select();
  $jsonlist3 = json_encode($list3);

  $moveusizelist="";
  for($i=1;$i<51;$i++){
      $moveusizelist.="<option  value='".$i."'>".$i."U</option>";
  }

  if (!empty($usize)){
      $myusize=$usize;
  }

  $this->assign('url_flag','listracks');
  $this->assign('areaname',$areaname);
  $this->assign('location_name',$location_name);
  $this->assign('objectid',$objectid);
  $this->assign('rackoption',$rackoption);
  $this->assign('itemusizeoption',$itemusizeoption);
  $this->assign('label',$label);
  $this->assign('common_name',$common_name);
  $this->assign('rackposdepth',$rackposdepth);
  $this->assign('ip',$ip);
  $this->assign('sn',$sn);
  $this->assign('status',$status);
  $this->assign('system',$system);
  $this->assign('model',$model);
  $this->assign('manufacturerid',$manufacturerid);
  $this->assign('typedesc',$typedesc);
  $this->assign('dname',$dname);
  $this->assign('comments',$comments);
  $this->assign('rackcomments',$rackcomments);
  $this->assign('racktable',$racktable);
  $this->assign('usizelist',$usizelist);
  $this->assign('moveusizelist',$moveusizelist);
  $this->assign('jsonlist2',$jsonlist2);
  $this->assign('jsonlist3',$jsonlist3);
  $this->assign('loclist',$str_);
  $this->assign('myusize',$myusize);
  $this->assign('block',$block);
  $this->assign('rackid',$rackid);
  $this->assign('ajax',$ajax);

//---------------flag=move1
  $this->assign('movelocationid',$movelocationid);
  $this->assign('moveareaid',$moveareaid);
  $this->assign('moverackid',$moverackid);
  $this->assign('movelocationname',$movelocationname);
  $this->assign('moveareaname',$moveareaname);
  $this->assign('movecomments',$movecomments);
  $this->assign('moveracktable',$moveracktable);
  $this->assign('flag',$flag);
//---------------flag=move1

  $this->display();  
}



public function  rackspace_img(){

    $flag=$_REQUEST['flag'];
    $rackid=$_REQUEST['rackid'];
    $imgdata=$_REQUEST['imgdata'];
    $imgdata = explode(",",$imgdata);
    $imgdata_new=$imgdata[1];
    $imgdata_new=base64_decode($imgdata_new);

/*    $Testasset =M("testasset");
    $adddata['name']=111;
    $adddata['nodestring']=$imgdata_new;
    $Testasset->add($adddata);*/
    

    $filepath='D:\xampp\htdocs\ly2\testimg\\'.$rackid.'.png';
    $this->writehtml($filepath,$imgdata_new);


    //$sPath='D:\xampp\htdocs\ly2\\';
    //$sFileName=$rackid;
    //echo $sBase64Img,$sPath,$sFileName;
    //$this->saveBase64Img($sBase64Img,$sPath,$sFileName);
     
}






/**
 * Base64 流形式图片保存为文件
 * @param string $sBase64Img
 * @param string $sPath 存放路径
 * @param string||null $sFileName 文件名
 * @return array|bool
 */
function saveBase64Img($sBase64Img, $sPath, $sFileName = null) {
    //匹配出图片的格式
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $sBase64Img, $result)) {
        $sType = $result[2];
        $sFileName=!empty($sFileName)?$sFileName:uniqid();
        $sPathFile = $sPath . '/' . $sFileName . '.' . $sType;
        if (!file_exists($sPathFile)) {
            mkdir($sPath, 07777, true);
        }
        if (file_put_contents($sPathFile, base64_decode(str_replace($result[1], '', $sBase64Img)))) {
            return array('path' => $sPath, 'filename' => $sFileName,'type'=>$sType);
    } else {
            return false;
        }
    }
    return false;
}








public function  rackspace_down(){

   if(!is_login()){
     $this->redirect("User/login");
   }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');
  $Ipdb_items =M("ipdb_items");
  $Department =M("department");


  $objectid= $_REQUEST['objectid'];
  $flag= 'down';
  empty($objectid) && $this->error('请输入设备ID');

  $list = $Ipdb_items->where('id = "'.$objectid.'"')->select();
  $rackid=$list[0]['rackid'];

//-----------------------------------log start--------------------------------------------
  $this->putrackslog($rackid,$objectid,$flag);
//-----------------------------------log end----------------------------------------------

  unset($downdata);
  $downdata['rackposdepth']=0;
  $downdata['rackposition']=0;
  $downdata['locationid']=0;
  $downdata['rackid']=0;
  $downdata['locareaid']=0;
  $downdata['appointment']=0;

  $Ipdb_items->where('id="'.$objectid.'"')->setField($downdata);
  $this->show('<script type="text/javascript" >alert("设备报废操作成功");window.location.href="{:U("Item/index")}"; </script>');
  exit();


  $this->assign('url_flag','listracks');
  $this->display();  
}



public function  rackspace_down_ajax(){

   if(!is_login()){
     $this->redirect("User/login");
   }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');
  $Ipdb_items =M("ipdb_items");
  $Department =M("department");

  $sn= $_REQUEST['sn'];
  $snlist = $Ipdb_items->where('sn = "'.$sn.'"')->select();
  $objectid=$snlist[0]['id'];

  $flag= 'down';
  if (empty($objectid)){
    $data['status']='error';
    //echo 'error';die;
  }  

  $list = $Ipdb_items->where('id = "'.$objectid.'"')->select();
  $rackid=$list[0]['rackid'];

//-----------------------------------log start--------------------------------------------
  if (!empty($rackid)){
      $this->putrackslog($rackid,$objectid,$flag);
  }
  
//-----------------------------------log end----------------------------------------------

  unset($downdata);
  $downdata['rackposdepth']='';
  $downdata['rackposition']='';
  $downdata['locationid']='';
  $downdata['rackid']='';
  $downdata['locareaid']='';
  $downdata['appointment']=0;

  $editlist=$Ipdb_items->where('id="'.$objectid.'"')->setField($downdata);
/*  $this->show('<script type="text/javascript" >alert("设备下架操作成功");window.location.href="{:U("Zichan/index")}"; </script>');
  exit();*/
  if($editlist){
      $data['status']='ok';
      //echo 'ok';die;
  }else{
      $data['status']='error';
      //echo 'error';die;
  }

  $this->ajaxReturn($data);
}








public function  rackspace_up_ajax(){

   if(!is_login()){
     $this->redirect("User/login");
   }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');
  $Ipdb_items =M("ipdb_items");
  $Department =M("department");
  $Itemtype =M("itemtype");
  $Ipdb_racks =M("ipdb_racks");
  $Ipdb_locations =M("ipdb_locations");
  $Ipdb_locareas =M("ipdb_locareas");



  $sn= $_REQUEST['sn'];
  $locareaid= $_REQUEST['locareaid'];
  $locationid= $_REQUEST['locationid'];
  $rackposdepth= $_REQUEST['rackposdepth'];
  $rackposition= $_REQUEST['rackposition'];
  $usize= $_REQUEST['usize'];
  $rackid= $_REQUEST['rackid'];
  $snlist = $Ipdb_items->where('sn = "'.$sn.'"')->select();
  $objectid=$snlist[0]['id'];
//  http://localhost/zichan_pure/index.php/Home/Racks/rackspace_up_ajax/locareaid/3/locationid/3/rackid/1/rackposition/1/rackposdepth/6/usize/2/sn/444.html

/*  $locareaid=$snlist[0]['locareaid'];
  $locationid=$snlist[0]['locationid'];
  $rackposdepth=6;
  $rackposition=$snlist[0]['rackposition'];
  $usize=$snlist[0]['usize'];
  $rackid=$snlist[0]['rackid'];*/

  $flag= 'up';
  $data['status']='ok';
  if (empty($objectid)){ $data['status']='error1';} 
  if (empty($locareaid)){ $data['status']='error2';} 
  if (empty($locationid)){ $data['status']='error3';} 
  if (empty($rackposdepth)){ $data['status']='error4';} 
  if (empty($rackposition)){ $data['status']='error5';} 
  if (empty($usize)){ $data['status']='error6';} 
  if (empty($rackid)){ $data['status']='error7';} 

if ($data['status']=='ok'){


//-----------------------------------判断U位是否占用 start----------------------------------------------

//--------------------获取当前设备要占U位id开始

        $mystart=$rackposition;
        if ($myusize==1){
          $myend=($rackposition+$myusize);
        }else{
          $myend=($rackposition+$myusize)-1;
        }
        //$mycount=$myend-$mystart;
        $my_alls='';
        for($j=0;$j<$myusize;$j++){ 
           $check_myrackposition=$mystart+$j;
           $my_alls=$my_alls.','.$check_myrackposition;
        }
        $my_alls=substr($my_alls,1,strlen($my_alls));
//--------------------获取当前设备要占U位id结束
        //echo $my_alls."------------<br />";


        $checkitemlist = $Ipdb_items->where('rackid = "'.$rackid.'" ')->select();
        $other_alls='';
        for($i=0;$i<count($checkitemlist);$i++){      
            $other_usize=$checkitemlist[$i]['usize'];
            $other_start=$checkitemlist[$i]['rackposition'];
            $other_end=($other_start+$other_usize)-1;

            $other_all='';
            for($ii=0;$ii<$other_usize;$ii++){ //2-5 共4U
                $check_otherrackposition=$other_start+$ii;
                $other_all=$other_all.','.$check_otherrackposition;
            }
            $other_all=substr($other_all,1,strlen($other_all));
            $other_alls=$other_alls.','.$other_all;
        }
        $other_alls=substr($other_alls,1,strlen($other_alls));

        //echo $other_alls."------------<br />";
        $my_allarrays=explode(',',$my_alls); 
        $other_allarrays=explode(',',$other_alls); 

        $my=array_intersect($my_allarrays,$other_allarrays);
        //var_dump($my);
        if (!empty($my)){
            $this->show('<script type="text/javascript" >alert("机柜U位已经被占用，请重新选择!");history.go(-1)</script>');
            exit();
        }
//-----------------------------------判断U位是否占用 end-------------------------------------------------


  $racktable=$this->showeditrackposdepth($rackid,$objectid); 

    unset($adddata);
    $adddata['rackposdepth']=6;
    $adddata['usize']=$usize;
    $adddata['rackposition']=$rackposition;
    $adddata['rackid']=I('post.rackid');
    $adddata['locationid']=$locationid;
    $adddata['rackid']=$rackid;
    $adddata['locareaid']=$areaid;
    $adddata['appointment']=0; //1=预留 0是正常
  

    $editlist=$Ipdb_items->where('id="'.$objectid.'"')->setField($adddata);
    if($editlist){
        $data['status']='ok';
        //echo 'ok';die;
    }else{
        $data['status']='error11';
        //echo 'error';die;
    }

}else{
    $data['status']='error12';
    //echo 'error';die;
}

 $this->ajaxReturn($data); 
}
























public function  rackspace_move(){

   if(!is_login()){
     $this->redirect("User/login");
   }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');
  $Ipdb_items =M("ipdb_items");
  $Department =M("department");
  $Itemtype =M("itemtype");
  $Ipdb_racks =M("ipdb_racks");
  $Ipdb_locations =M("ipdb_locations");
  $Ipdb_locareas =M("ipdb_locareas");



  $objectid= $_REQUEST['objectid'];
  $flag= $_REQUEST['flag'];
  empty($objectid) && $this->error('请输入id');

  $block= $_REQUEST['block'];
  if ($flag=='move1'){
      $block='';
  }

  if ($flag=='obligateok'){
    $okdata['appointment']=0;
    $Ipdb_items->where('id="'.$objectid.'"')->setField($okdata);
    $this->show('<script type="text/javascript" >alert("设备预留变更为上架成功");window.location.href="{:U(\"Racks/rackspace?objectid='.$objectid.'\")}"; </script>');
    exit();
  }
    



  $list = $Ipdb_items->where('id = "'.$objectid.'"')->select();
  $label=$list[0]['label'];
  $ip=$list[0]['ip'];
  $sn=$list[0]['sn'];
  $comments=$list[0]['comments'];
  $usize=$list[0]['usize'];
  $rackid=$list[0]['rackid'];
  $rackposition=$list[0]['rackposition'];
  $system=$list[0]['system'];
  $model=$list[0]['model'];
  $rackposdepth=$list[0]['rackposdepth'];
  $manufacturerid=$list[0]['manufacturerid'];
  $locareaid=$list[0]['locareaid'];
  $locationid=$list[0]['locationid'];
  $appointment=$list[0]['appointment'];
  $common_name=$list[0]['common_name'];

  if ($appointment==1){$status='<font color="#FFD700">预留</font> &nbsp;&nbsp;>> &nbsp;&nbsp;<a href="'.__ROOT__.'/index.php/Home/Racks/rackspace_move/flag/obligateok/objectid/'.$objectid.'.html">变更上架</a>';}
  if (empty($locationid) && empty($locareaid) && empty($rackid)){$status='<font color="">下架</font>';}
  if (!empty($locationid) && empty($appointment) && !empty($locareaid) && !empty($rackid)){$status='<font color="#00FFFF">上架</font>';}



  $itemtypeid=$list[0]['itemtypeid'];
  $typelist = $Itemtype->where('id = "'.$itemtypeid.'"')->select();
  $typedesc=$typelist[0]['typedesc'];

  $departmentid=$list[0]['departmentid'];
  $dlist = $Department->where('id = "'.$departmentid.'"')->select();
  $dname=$dlist[0]['name'];

  $racklist = $Ipdb_racks->where('id = "'.$rackid.'"')->select();
  $usizecount=$racklist[0]['usize'];
  $revnums=$racklist[0]['revnums'];
  $rackcomments=$racklist[0]['name'];

  $locationlist = $Ipdb_locations->where('id = "'.$locationid.'"')->select();
  $location_name=$locationlist[0]['name'];
  $locarealist = $Ipdb_locareas->where('id = "'.$locareaid.'"')->select();
  $areaname=$locarealist[0]['areaname'];
  $is_store = $locationlist[0]['is_store'];


  $racktable=$this->showeditrackposdepth($rackid,$objectid); 

  if(empty($usizecount)){
      $usizelist="";
      for($i=1;$i<51;$i++){
          $usizelist.="<option  value='".$i."'>".$i."U</option>";
      }

      $itemusizeoption=$usizelist;


  }else{
      //---------------------------------U位选择
        $usizelist="";
        for($i=1;$i<=$usizecount;$i++){
          if ($i==$rackposition){
            $usizelist.="<option  value='".$i."' selected='selected'>".$i."U</option>";
          }else{
            $usizelist.="<option  value='".$i."'>".$i."U</option>";
          }
        }  

      //---------------------------------机柜位置选择
      $itemusizeoption="";
      for($i=1;$i<=$usizecount;$i++){
        if ($i==$usize){
          $itemusizeoption.="<option  value='".$i."' selected='selected'>".$i."U</option>";
        }else{
          $itemusizeoption.="<option  value='".$i."'>".$i."U</option>";
        }
      }

  }






//---------------------------------机柜选择
  //$racklist = $Ipdb_racks->select();
  $option='';
  $racklist=$Ipdb_racks->order('id asc')->select();
  $rackoption= '<option value ="" >请选择机柜...</option>';
  for($i=0;$i<count($racklist);$i++){      
      $depid=$racklist[$i]['id'];
      $tempcomments=$racklist[$i]['name'];
      if ($depid==$rackid){
        $rackoption.='<option value ="'.$depid.'" selected="selected">'.$tempcomments.'</option>';
      }else{
        $rackoption.='<option value ="'.$depid.'" >'.$tempcomments.'</option>';
      }
      
  }


  if(IS_POST){
      $flag= $_REQUEST['flag'];//设备id

    if($flag=='move'){

        $objectid= $_REQUEST['objectid'];//设备id
        $myusize= $_REQUEST['myusize'];//设备U大小
        $locationid= $_REQUEST['locationid'];//机房id
        $areaid= $_REQUEST['areaid'];//房间ID
        $rackid= $_REQUEST['rackid'];//机柜ID
        $rackposition= $_REQUEST['rackposition'];//机架起始位置
        $rackposdepthid= $_REQUEST['rackposdepthid'];//机架深度ID
        empty($objectid) && $this->error('请输入设备ID!');
        empty($myusize) && $this->error('请输入设备U大小!');
        empty($locationid) && $this->error('请输入机房ID!');
        empty($areaid) && $this->error('请输入房间ID!');
        empty($rackid) && $this->error('请输入机柜ID!');
        empty($rackposition) && $this->error('请输入机架起始位置!');
        empty($rackposdepthid) && $this->error('请输入机架深度ID!');
        
        //查看原来机柜location信息
        
        
        

        
//-----------------------------------判断U位是否占用 start----------------------------------------------

//--------------------获取当前设备要占U位id开始

        $mystart=$rackposition;
        if ($myusize==1){
          $myend=($rackposition+$myusize);
        }else{
          $myend=($rackposition+$myusize)-1;
        }
        
        
        //$mycount=$myend-$mystart;
        $my_alls='';
        for($j=0;$j<$myusize;$j++){ 
           $check_myrackposition=$mystart+$j;
           $my_alls=$my_alls.','.$check_myrackposition;
        }
        $my_alls=substr($my_alls,1,strlen($my_alls));
//--------------------获取当前设备要占U位id结束
        //echo $my_alls."------------<br />";
        //rack信息
        $rack_info_now = $Ipdb_racks->where('id = "'.$rackid.'" ')->find();

        $checkitemlist = $Ipdb_items->where('rackid = "'.$rackid.'" ')->select();
        $other_alls='';
        for($i=0;$i<count($checkitemlist);$i++){      
            $other_usize=$checkitemlist[$i]['usize'];
            $other_start=$checkitemlist[$i]['rackposition'];
            $other_end=($other_start+$other_usize)-1;

            $other_all='';
            for($ii=0;$ii<$other_usize;$ii++){ //2-5 共4U
                $check_otherrackposition=$other_start+$ii;
                $other_all=$other_all.','.$check_otherrackposition;
            }
            $other_all=substr($other_all,1,strlen($other_all));
            $other_alls=$other_alls.','.$other_all;
        }
        $other_alls=substr($other_alls,1,strlen($other_alls));

        //echo $other_alls."------------<br />";
        $my_allarrays=explode(',',$my_alls); 
        $other_allarrays=explode(',',$other_alls); 

        $my=array_intersect($my_allarrays,$other_allarrays);
        //var_dump($my);
        if (!empty($my)){
            $this->show('<script type="text/javascript" >alert("机柜U位已经被占用，请重新选择!");history.go(-1)</script>');
            exit();
        }
        
        $rack_usize = intval($rack_info_now['usize']);
        if ($myend > $rack_usize) {
            $this->show('<script type="text/javascript" >alert("超出机柜范围，请重新选择!");history.go(-1)</script>');
            exit();
        }
//-----------------------------------判断U位是否占用 end-------------------------------------------------




//-----------------------------------log start----------------------------------------------
        $movearray['moveareaid']=$areaid;
        $movearray['movelocationid']=$locationid;
        $movearray['moverackposition']=$rackposition;
        $movearray['movemyusize']=$myusize;
        $movearray['moverackid']=$rackid;

        $this->putrackslog($rackid,$objectid,$flag,$movearray);
//-----------------------------------log end----------------------------------------------


        unset($editddata);
        $editddata['rackposdepth']=$rackposdepthid;
        $editddata['rackposition']=$rackposition;
        $editddata['locationid']=$locationid;
        $editddata['rackid']=$rackid;
        $editddata['locareaid']=$areaid;
        $editddata['appointment']=0;

        $Ipdb_items->where('id="'.$objectid.'"')->setField($editddata);
        $this->show('<script type="text/javascript" >alert("迁移变更成功");window.location.href="{:U(\"Racks/rackspace_move?block=block&objectid='.$objectid.'\")}"; </script>');
        exit();

    }elseif ($flag=='move1'){

        $objectid= $_REQUEST['objectid'];//设备id
        $myusize= $_REQUEST['myusize'];//设备U大小
        $movelocationid= $_REQUEST['locationid'];//机房id
        $moveareaid= $_REQUEST['areaid'];//房间ID
        $moverackid= $_REQUEST['rackid'];//机柜ID

        empty($objectid) && $this->error('请输入设备ID!');
        empty($myusize) && $this->error('请输入设备U大小!');
        empty($movelocationid) && $this->error('请输入机房ID!');
        empty($moveareaid) && $this->error('请输入房间ID!');
        empty($moverackid) && $this->error('请输入机柜ID!');
        
        
        $object_info = M('ipdb_items')->alias('items')->join('vnet_ipdb_locations locations ON items.locationid = locations.id')->where("items.id=$objectid ")->select();
        $is_store_now = $object_info[0]['is_store'];
        
        $this->assign('is_store_now',$is_store_now); //原来机柜

        $mlocarealist = $Ipdb_locareas->where('id = "'.$moveareaid.'"')->select();
        $moveareaname=$mlocarealist[0]['areaname'];

        $mracklist = $Ipdb_racks->where('id = "'.$moverackid.'"')->select();
        $movecomments=$mracklist[0]['name'];
        $moveusize=$mracklist[0]['usize'];
        
        $mlocationlist = $Ipdb_locations->where('id = "'.$movelocationid.'"')->select();
        $movelocationname=$mlocationlist[0]['name'];
        

        $is_store =  intval($mlocationlist[0]['is_store']);
        if ($is_store == 1) {
            $editddata['rackposition']=0;
            $editddata['locationid']=$movelocationid;
            $editddata['rackid']=$moverackid;
            $editddata['locareaid']=$moveareaid;
            $editddata['appointment']=0;
    
            $Ipdb_items->where('id="'.$objectid.'"')->setField($editddata);
            $this->show('<script type="text/javascript" >alert("迁移变更成功");window.location.href="{:U(\"Item/item_info?id='.$objectid.'\")}"; </script>');
            exit();
        }

//---------------------------------------------------------------show 机柜信息
    
        $moveracktable=$this->showrackposdepth($moverackid,$objectid);
        $racktable=$this->showeditrackposdepth($rackid,$objectid);
//------------------------------------------------------------------机柜信息结束        

      //---------------------------------U位选择
        $moveusizelist="";
        for($i=1;$i<=$moveusize;$i++){
            $moveusizelist.="<option  value='".$i."'>".$i."U</option>";
        } 

    }


  }


//-----------------------------------------------一二级联动
  $loclist=$Ipdb_locations->select();
  $str_= '<option value ="" >请选择数据中心...</option>';
  for($i=0;$i<count($loclist);$i++){      
      $id=$loclist[$i]['id'];
      $locname=$loclist[$i]['name'];
      $str_.='<option value ="'.$id.'" >'.$locname.'</option>';
  }
  $list2=$Ipdb_locareas->field('id,areaname,locationid')->select();
  $jsonlist2 = json_encode($list2);

  $list3=$Ipdb_racks->field('id,locationid,locareaid,name')->select();
  $jsonlist3 = json_encode($list3);

/*  $moveusizelist="";
  for($i=1;$i<51;$i++){
      $moveusizelist.="<option  value='".$i."'>".$i."U</option>";
  }*/

  if (!empty($usize)){
      $myusize=$usize;
  }


  //$this->assign('url_flag','listracks');
  $this->assign('areaname',$areaname);
  $this->assign('location_name',$location_name);
  $this->assign('objectid',$objectid);
  $this->assign('rackoption',$rackoption);
  $this->assign('itemusizeoption',$itemusizeoption);
  $this->assign('label',$label);
  $this->assign('rackposdepth',$rackposdepth);
  $this->assign('ip',$ip);
  $this->assign('sn',$sn);
  $this->assign('status',$status);
  $this->assign('system',$system);
  $this->assign('model',$model);
  $this->assign('manufacturerid',$manufacturerid);
  $this->assign('typedesc',$typedesc);
  $this->assign('dname',$dname);
  $this->assign('comments',$comments);
  $this->assign('common_name',$common_name);
  $this->assign('rackcomments',$rackcomments);
  $this->assign('racktable',$racktable);
  $this->assign('usizelist',$usizelist);
  $this->assign('jsonlist2',$jsonlist2);
  $this->assign('jsonlist3',$jsonlist3);
  $this->assign('loclist',$str_);
  $this->assign('myusize',$myusize);
  $this->assign('block',$block);

//---------------flag=move1
  $this->assign('movelocationid',$movelocationid);
  $this->assign('moveareaid',$moveareaid);
  $this->assign('moverackid',$moverackid);
  $this->assign('movelocationname',$movelocationname);
  $this->assign('moveareaname',$moveareaname);
  $this->assign('movecomments',$movecomments);
  $this->assign('moveracktable',$moveracktable);
  $this->assign('moveusizelist',$moveusizelist);

  $this->assign('flag',$flag);
  $this->assign('is_store',$is_store); //目标机柜
  $this->assign('url_flag','item_index');
//---------------flag=move1

  $this->display();  
}








public function  rackspace_obligate(){

   if(!is_login()){
     $this->redirect("User/login");
   }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');
  $Ipdb_items =M("ipdb_items");
  $Department =M("department");
  $Itemtype =M("itemtype");
  $Ipdb_racks =M("ipdb_racks");
  $Ipdb_locations =M("ipdb_locations");
  $Ipdb_locareas =M("ipdb_locareas");



  $objectid= $_REQUEST['objectid'];
  $flag= $_REQUEST['flag'];
  empty($objectid) && $this->error('请输入id');

  $block= $_REQUEST['block'];
  if ($flag=='move1'){
      $block='';
  }


    



  $list = $Ipdb_items->where('id = "'.$objectid.'"')->select();
  $label=$list[0]['label'];
  $ip=$list[0]['ip'];
  $sn=$list[0]['sn'];
  $comments=$list[0]['comments'];
  $usize=$list[0]['usize'];
  $rackid=$list[0]['rackid'];
  $rackposition=$list[0]['rackposition'];
  $system=$list[0]['system'];
  $model=$list[0]['model'];
  $rackposdepth=$list[0]['rackposdepth'];
  $manufacturerid=$list[0]['manufacturerid'];
  $locareaid=$list[0]['locareaid'];
  $locationid=$list[0]['locationid'];
  $appointment=$list[0]['appointment'];
  $common_name=$list[0]['common_name'];

  if ($appointment==1){$status='<font color="#FFD700">预留</font> &nbsp;&nbsp;>> &nbsp;&nbsp;<a href="'.__ROOT__.'/index.php/Home/Racks/rackspace_obligate/flag/obligateok/objectid/'.$objectid.'.html">变更上架</a>';}
  if (empty($locationid) && empty($locareaid) && empty($rackid)){$status='<font color="">下架</font>';}
  if (!empty($locationid) && empty($appointment) && !empty($locareaid) && !empty($rackid)){$status='<font color="#00FFFF">上架</font>';}


  if ($flag=='obligateok'){
//-----------------------------------log start--------------------------------------------
        $this->putrackslog($rackid,$objectid,$flag);
//-----------------------------------log end----------------------------------------------
    $okdata['appointment']=0;
    $Ipdb_items->where('id="'.$objectid.'"')->setField($okdata);
    $this->show('<script type="text/javascript" >alert("设备预留变更为上架成功");window.location.href="{:U(\"Racks/rackspace_obligate?block=block&objectid='.$objectid.'\")}"; </script>');
    exit();
  }


  $itemtypeid=$list[0]['itemtypeid'];
  $typelist = $Itemtype->where('id = "'.$itemtypeid.'"')->select();
  $typedesc=$typelist[0]['typedesc'];

  $departmentid=$list[0]['departmentid'];
  $dlist = $Department->where('id = "'.$departmentid.'"')->select();
  $dname=$dlist[0]['name'];

  $racklist = $Ipdb_racks->where('id = "'.$rackid.'"')->select();
  $usizecount=$racklist[0]['usize'];
  $revnums=$racklist[0]['revnums'];
  $rackcomments=$racklist[0]['comments'];

  $locationlist = $Ipdb_locations->where('id = "'.$locationid.'"')->select();
  $location_name=$locationlist[0]['name'];
  $locarealist = $Ipdb_locareas->where('id = "'.$locareaid.'"')->select();
  $areaname=$locarealist[0]['areaname'];


  $racktable=$this->showeditrackposdepth($rackid,$objectid); 

  if(empty($usizecount)){
      $usizelist="";
      for($i=1;$i<51;$i++){
          $usizelist.="<option  value='".$i."'>".$i."U</option>";
      }

      $itemusizeoption=$usizelist;


  }else{
      //---------------------------------U位选择
        $usizelist="";
        for($i=1;$i<=$usizecount;$i++){
          if ($i==$rackposition){
            $usizelist.="<option  value='".$i."' selected='selected'>".$i."U</option>";
          }else{
            $usizelist.="<option  value='".$i."'>".$i."U</option>";
          }
        }  

      //---------------------------------机柜位置选择
      $itemusizeoption="";
      for($i=1;$i<=$usizecount;$i++){
        if ($i==$usize){
          $itemusizeoption.="<option  value='".$i."' selected='selected'>".$i."U</option>";
        }else{
          $itemusizeoption.="<option  value='".$i."'>".$i."U</option>";
        }
      }

  }






//---------------------------------机柜选择
  //$racklist = $Racks->select();
  $option='';
  $racklist=$Ipdb_racks->order('id asc')->select();
  $rackoption= '<option value ="" >请选择机柜...</option>';
  for($i=0;$i<count($racklist);$i++){      
      $depid=$racklist[$i]['id'];
      $tempcomments=$racklist[$i]['comments'];
      if ($depid==$rackid){
        $rackoption.='<option value ="'.$depid.'" selected="selected">'.$tempcomments.'</option>';
      }else{
        $rackoption.='<option value ="'.$depid.'" >'.$tempcomments.'</option>';
      }
      
  }


  if(IS_POST){
      $flag= $_REQUEST['flag'];//设备id

    if ($flag=='obligate'){
        $objectid= $_REQUEST['objectid'];//设备id
        $rackposdepthid= $_REQUEST['rackposdepthid'];//机柜深度id
        $rackposition= $_REQUEST['rackposition'];//设备起始U位
        $myusize= $_REQUEST['myusize'];//设备U大小
        $locationid= $_REQUEST['locationid'];//机房id
        $areaid= $_REQUEST['areaid'];//房间ID
        $rackid= $_REQUEST['rackid'];//机柜ID

        empty($objectid) && $this->error('请输入设备ID!');
        empty($rackposdepthid) && $this->error('请输入机柜深度!');
        empty($rackposition) && $this->error('请输入设备起始U位!');
        empty($myusize) && $this->error('请输入设备U大小!');
        empty($locationid) && $this->error('请输入机房ID!');
        empty($areaid) && $this->error('请输入房间ID!');
        empty($rackid) && $this->error('请输入机柜ID!');

//-----------------------------------判断U位是否占用 start----------------------------------------------

//--------------------获取当前设备要占U位id开始

        $mystart=$rackposition;
        if ($myusize==1){
          $myend=($rackposition+$myusize);
        }else{
          $myend=($rackposition+$myusize)-1;
        }
        //$mycount=$myend-$mystart;
        $my_alls='';
        for($j=0;$j<$myusize;$j++){ 
           $check_myrackposition=$mystart+$j;
           $my_alls=$my_alls.','.$check_myrackposition;
        }
        $my_alls=substr($my_alls,1,strlen($my_alls));
//--------------------获取当前设备要占U位id结束
        //echo $my_alls."------------<br />";


        $checkitemlist = $Ipdb_items->where('rackid = "'.$rackid.'" ')->select();
        $other_alls='';
        for($i=0;$i<count($checkitemlist);$i++){      
            $other_usize=$checkitemlist[$i]['usize'];
            $other_start=$checkitemlist[$i]['rackposition'];
            $other_end=($other_start+$other_usize)-1;

            $other_all='';
            for($ii=0;$ii<$other_usize;$ii++){ //2-5 共4U
                $check_otherrackposition=$other_start+$ii;
                $other_all=$other_all.','.$check_otherrackposition;
            }
            $other_all=substr($other_all,1,strlen($other_all));
            $other_alls=$other_alls.','.$other_all;
        }
        $other_alls=substr($other_alls,1,strlen($other_alls));

        //echo $other_alls."------------<br />";
        $my_allarrays=explode(',',$my_alls); 
        $other_allarrays=explode(',',$other_alls); 

        $my=array_intersect($my_allarrays,$other_allarrays);
        //var_dump($my);
        if (!empty($my)){
            $this->show('<script type="text/javascript" >alert("机柜U位已经被占用，请重新选择!");history.go(-1)</script>');
            exit();
        }
//-----------------------------------判断U位是否占用 end-------------------------------------------------

//-----------------------------------log start--------------------------------------------
        $this->putrackslog($rackid,$objectid,$flag);
//-----------------------------------log end----------------------------------------------

        unset($resdata);
        $resdata['rackposdepth']=I('post.rackposdepthid');
        $resdata['usize']=I('post.myusize');
        $resdata['rackposition']=I('post.rackposition');
        $resdata['rackid']=I('post.rackid');
        $resdata['locationid']=$locationid;
        $resdata['rackid']=$rackid;
        $resdata['locareaid']=$areaid;
        $resdata['appointment']=1;

        $Ipdb_items->where('id="'.$objectid.'"')->setField($resdata);
        $this->show('<script type="text/javascript" >alert("设备预留成功");window.location.href="{:U(\"Racks/rackspace_obligate?block=block&objectid='.$objectid.'\")}"; </script>');
        exit();
    }elseif ($flag=='obligate1'){

        $objectid= $_REQUEST['objectid'];//设备id
        $myusize= $_REQUEST['myusize'];//设备U大小
        $movelocationid= $_REQUEST['locationid'];//机房id
        $moveareaid= $_REQUEST['areaid'];//房间ID
        $moverackid= $_REQUEST['rackid'];//机柜ID

        empty($objectid) && $this->error('请输入设备ID!');
        empty($myusize) && $this->error('请输入设备U大小!');
        empty($movelocationid) && $this->error('请输入机房ID!');
        empty($moveareaid) && $this->error('请输入房间ID!');
        empty($moverackid) && $this->error('请输入机柜ID!');

        $mlocarealist = $Ipdb_locareas->where('id = "'.$moveareaid.'"')->select();
        $moveareaname=$mlocarealist[0]['areaname'];

        $mracklist = $Ipdb_racks->where('id = "'.$moverackid.'"')->select();
        $movecomments=$mracklist[0]['comments'];

        $mlocationlist = $Ipdb_locations->where('id = "'.$movelocationid.'"')->select();
        $movelocationname=$mlocationlist[0]['name'];

//---------------------------------------------------------------show 机柜信息
    
        $moveracktable=$this->showrackposdepth($moverackid,$objectid);

//------------------------------------------------------------------机柜信息结束        



    }






  }


//-----------------------------------------------一二级联动
  $loclist=$Ipdb_locations->select();
  $str_= '<option value ="" >请选择数据中心...</option>';
  for($i=0;$i<count($loclist);$i++){      
      $id=$loclist[$i]['id'];
      $locname=$loclist[$i]['name'];
      $str_.='<option value ="'.$id.'" >'.$locname.'</option>';
  }
  $list2=$Ipdb_locareas->field('id,areaname,locationid')->select();
  $jsonlist2 = json_encode($list2);

  $list3=$Ipdb_racks->field('id,locationid,locareaid,comments')->select();
  $jsonlist3 = json_encode($list3);

  $moveusizelist="";
  for($i=1;$i<51;$i++){
      $moveusizelist.="<option  value='".$i."'>".$i."U</option>";
  }


  if (!empty($usize)){
      $myusize=$usize;
  }

  $this->assign('url_flag','listracks');
  $this->assign('areaname',$areaname);
  $this->assign('location_name',$location_name);
  $this->assign('objectid',$objectid);
  $this->assign('rackoption',$rackoption);
  $this->assign('itemusizeoption',$itemusizeoption);
  $this->assign('label',$label);
  $this->assign('rackposdepth',$rackposdepth);
  $this->assign('ip',$ip);
  $this->assign('sn',$sn);
  $this->assign('status',$status);
  $this->assign('system',$system);
  $this->assign('model',$model);
  $this->assign('manufacturerid',$manufacturerid);
  $this->assign('typedesc',$typedesc);
  $this->assign('dname',$dname);
  $this->assign('comments',$comments);
  $this->assign('rackcomments',$rackcomments);
  $this->assign('racktable',$racktable);
  $this->assign('usizelist',$usizelist);
  $this->assign('moveusizelist',$moveusizelist);
  $this->assign('jsonlist2',$jsonlist2);
  $this->assign('jsonlist3',$jsonlist3);
  $this->assign('loclist',$str_);
  $this->assign('myusize',$myusize);
  $this->assign('block',$block);
  $this->assign('common_name',$common_name);
//---------------flag=move1
  $this->assign('movelocationid',$movelocationid);
  $this->assign('moveareaid',$moveareaid);
  $this->assign('moverackid',$moverackid);
  $this->assign('movelocationname',$movelocationname);
  $this->assign('moveareaname',$moveareaname);
  $this->assign('movecomments',$movecomments);
  $this->assign('moveracktable',$moveracktable);
  $this->assign('flag',$flag);
//---------------flag=move1

  $this->display();  
}




function writehtml($file,$data){

   $fp = @fopen($file,wb);
   @flock($fp,LOCK_EX);
   fwrite($fp,$data);

   @flock($fp,LOCK_UN);
   fclose($fp);

} 

















}?>
<?php
namespace  Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class NodeController extends HomeController{




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






public function listnodes(){
    if(!is_login()){
      $this->redirect("User/login");
    }    
    $uid        =   is_login();
    $nickname = M('user')->getFieldByUid($uid, 'username');

    $contypeid=$_REQUEST['contypeid'];
    $nodename=$_REQUEST['nodename'];
    $Ipdb_node =M("ipdb_node");
    
    $order='sort asc';

    if (!empty($contypeid)){
        $count= $Ipdb_node->where(' contypeid="'.$contypeid.'" ')->count();
        $page = new \Think\Page($count,50);
        $list=$Ipdb_node->where(' contypeid="'.$contypeid.'" ')->limit($page->firstRow.','.$page->listRows)->order($order)->select(); 
    }else{
        $count= $Ipdb_node->where(' contypeid=0 ')->count();
        $page = new \Think\Page($count,50);
        $list=$Ipdb_node->where(' contypeid=0 ')->limit($page->firstRow.','.$page->listRows)->order($order)->select();      
    }

    
    for($i=0;$i<count($list);$i++){
        $temppid=$list[$i]['contypeid'];
        $checklist= $Ipdb_node->where(' id="'.$temppid.'" ')->select();
        $list[$i]['pidstr']=$checklist[0]['name'];
        if (empty($list[$i]['pidstr'])){$list[$i]['pidstr']='无';}

    }


    $this->assign('url_flag','listnodes');
    $this->assign('uid',$uid);
    $this->assign('nickname',$nickname);
    $this->assign('description',$description);
    $this->assign('sortorder',$sortorder); 
    $this->assign('sort',$sort); 
    $this->assign('nodename',$nodename); 
    $page->setConfig('header','共');
    $page->setConfig('first','«');
    $page->setConfig('prev','‹');
    $page->setConfig('next','›');
    $page->setConfig('last','»');
    $page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
    $show = $page->show();

    $this->assign('list',$list);
    $this->assign('page',$show);
    $this->display();

  }













public function  index(){

  //  if(!is_login()){
  //    $this->redirect("User/login");
  //  }    

  // $uid        =   is_login();
  // $nickname = M('Member')->getFieldByUid($uid, 'nickname');
  $Ipdb_node =M("ipdb_node");
  $Testasset =M("testasset");
  $typeid= $_REQUEST['id'];
  $name= $_REQUEST['name'];

  $list = $Ipdb_node->where('contypeid =0 ')->select();
  $showmenu=array();
  for($i=0;$i<count($list);$i++){

      $id=$list[$i]['id'];
      $name=$list[$i]['name'];


    $showmenu[$i]['name']=$name;
    if (in_array($typeid,$checkall_nodes)){ $showmenu[$i]['open']=true;} 
    $showmenu[$i]['children']=$submenu;
    $showmenu[$i]['target']='_self';
    $showmenu[$i]['url']='http://localhost/zichan_pure/index.php/Home/Node/index/id/'.$id.'.html';//'http://localhost/ztree/index.php?id='.$subid
    unset($submenu);
  }

  $json_showmenu = json_encode($showmenu);


  $url=$this->checkurl();
  $this->assign('url',$url); 
  $this->assign('json_showmenu',$json_showmenu);
  //$this->ajaxReturn($showmenu);
  $this->display();  
}




public function  index2(){

  //  if(!is_login()){
  //    $this->redirect("User/login");
  //  }    

  // $uid        =   is_login();
  // $nickname = M('Member')->getFieldByUid($uid, 'nickname');
  $Ipdb_locations =M("ipdb_locations");
  $Ipdb_locareas =M("ipdb_locareas");
  $Racks =M("racks");

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
    $list = $Racks->order('id desc')->select();
  }else{
    $list = $Racks->where('locationid in ('.$lids.') ')->order('id desc')->select();
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


  $url=$this->checkurl();
  $this->assign('url',$url); 
  $this->assign('json_showmenu',$json_showmenu);
  $this->assign('list',$list);
  $this->assign('showlabel',$showlabel);
  $this->display();  
}





public function  index3(){

  //  if(!is_login()){
  //    $this->redirect("User/login");
  //  }    

  // $uid        =   is_login();
  // $nickname = M('Member')->getFieldByUid($uid, 'nickname');
  $Ipdb_locations =M("ipdb_locations");
  $Ipdb_locareas =M("ipdb_locareas");
  $Racks =M("racks");

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
    $list = $Racks->order('id desc')->select();
  }else{
    $list = $Racks->where('locationid in ('.$lids.') ')->order('id desc')->select();
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


  $url=$this->checkurl();
  $this->assign('url',$url); 
  $this->assign('json_showmenu',$json_showmenu);
  $this->assign('list',$list);
  $this->assign('showlabel',$showlabel);
  $this->display();  
}







public function addnode(){

    header("Content-type: text/html; charset=utf-8");
    if(!is_login()){
        $this->redirect("User/login");
    }   
    $uid        =   is_login();
    $nickname = M('user')->getFieldByUid($uid, 'username');

    $Ipdb_node =M("ipdb_node");
    $aDataList=$Ipdb_node->order(' id asc')->select();//where('contypeid=0')->

    $aDataList = $this->array_column($aDataList, null, 'id');

    $aTreeList = $this->getTreeList(0, $aDataList);//获取 下面 层次 用 getTreeList
    $contypeoption = $this->createOptions($aTreeList);


    if(IS_POST){
      $name=I('post.name');

      $contypeid=I('post.contypeid');
      $map['name']=I('post.name');
      $map['sort']=I('post.sort');
      $map['contypeid']=I('post.contypeid');

      $list = $Ipdb_node->where('name="'.$name.'" and contypeid="'.$contypeid.'" ')->select();
      if(!empty($list)){
          $this->error('该节点标题已经存在!');
      }
      if($Ipdb_node->add($map)){
          $this->show('<script type="text/javascript" >alert("节点添加成功");window.location.href="{:U("listnodes")}"; </script>');
      }else{
          $this->show('<script type="text/javascript" >alert("节点添加失败");window.location.href="{:U("addnode")}"; </script>');
      }
      exit();
    }

    $this->assign('url_flag','listnodes');
    $this->assign('url',$url);
    $this->assign('uid',$uid);
    $this->assign('nickname',$nickname);
    $this->assign('contypeoption',$contypeoption);
    $this->display();
  }




public function editnode(){

    header("Content-type: text/html; charset=utf-8");
    if(!is_login()){
        $this->redirect("User/login");
    }   
    $uid        =   is_login();
    $nickname = M('user')->getFieldByUid($uid, 'username');


    $id   =  $_REQUEST['id'];
    $contypeid   =  $_REQUEST['contypeid'];
    empty($id) && $this->error('ID不能为空');

    $Ipdb_node =M("ipdb_node");
    $list=$Ipdb_node->where('id="'.$id.'"')->select();
    $name=$list[0]['name'];
    $sort=$list[0]['sort'];
      
    if(IS_POST){

        $id   =  $_REQUEST['id'];
        $name   =  $_REQUEST['name'];
        $sort   =  $_REQUEST['sort'];
        empty($id) && $this->error('ID不能为空');
        empty($name) && $this->error('ID不能为空');

        $editdata['name']=$name;
        $editdata['sort']=$sort;
        $Ipdb_node->where('id="'.$id.'"')->setField($editdata);
        $this->show('<script type="text/javascript" >alert("节点修改成功");window.location.href="{:U("listnodes?contypeid='.$contypeid.'")}"; </script>');

    }

    $this->assign('url_flag','listnodes');
    $this->assign('uid',$uid);
    $this->assign('nickname',$nickname);
    $this->assign('name',$name);
    $this->assign('id',$id);
    $this->assign('sort',$sort);
    $this->display();
  }




public function  nodedelete(){

  header("Content-type: text/html; charset=utf-8");
  if(!is_login()){
      $this->redirect("User/login");
  }   
  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');

  $id   =  $_REQUEST['id'];
  empty($id) && $this->error('ID不能为空');

  $Ipdb_node =M("ipdb_node");

  $Ipdb_node->where('id="'.$id.'"')->delete();
  $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("listnodes")}"; </script>'); 
  exit();

}







}?>
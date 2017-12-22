<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class  CustomerController extends HomeController {
    
    public function index(){
        $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表;
        
        $name= trim($_REQUEST['name']);
        if (empty($name)) {
            $count = $Model->query("SELECT count(*) as num FROM (SELECT DISTINCT(name) as name FROM vnet_zz_port) as na");
            $num = $count[0]['num'];
            $page = new \Think\Page($num,15);
            $pre = $page->firstRow;
            $allow = $page->listRows;
            $list=  $Model->query("SELECT DISTINCT(name) as name,code FROM vnet_zz_port limit $pre,$allow");
        }else{             
            $count = $Model->query("SELECT count(*) as num FROM (SELECT DISTINCT(name) as name FROM vnet_zz_port where name like '%$name%') as na");
            $num = $count[0]['num'];
            $page = new \Think\Page($num,15);
            $pre = $page->firstRow;
            $allow = $page->listRows;
            $list=  $Model->query("SELECT DISTINCT(name) as name,code FROM vnet_zz_port where name like '%$name%' limit $pre,$allow");
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
        
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('url_flag','customer_index');
        $this->assign('name',$name);
        $this->display();
    }
    
    
    public function ip_index(){
        $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表;
        $name= trim($_REQUEST['name']);
        if (empty($name)) {
            $count = $Model->query("SELECT count(*) as num FROM (SELECT DISTINCT(name) as name FROM vnet_zz_ip) as na");
            $num = $count[0]['num'];
            $page = new \Think\Page($num,15);
            $pre = $page->firstRow;
            $allow = $page->listRows;
            $list=  $Model->query("SELECT DISTINCT(name) as name,code FROM vnet_zz_ip limit $pre,$allow");
        }else{
            $count = $Model->query("SELECT count(*) as num FROM (SELECT DISTINCT(name) as name FROM vnet_zz_ip where name like '%$name%') as na");
            $num = $count[0]['num'];
            $page = new \Think\Page($num,15);
            $pre = $page->firstRow;
            $allow = $page->listRows;
            $list=  $Model->query("SELECT DISTINCT(name) as name,code FROM vnet_zz_ip where name like '%$name%' limit $pre,$allow");
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
    
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('url_flag','customer_index');
        $this->assign('name',$name);
        $this->display();
    }
    
    public function all_index(){
        $demo = M('zz_company');
        $name= trim($_REQUEST['name']);
        if (empty($name)) {
            $count = $demo->count();
            $page = new \Think\Page($count,15);
            $list=$demo->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $where['name'] = array('like',"%$name%");
            $count = $demo->where($where)->count();
            $page = new \Think\Page($count,15);
            $list=$demo->where($where)->limit($page->firstRow.','.$page->listRows)->select();
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
    
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('url_flag','customer_index');
        $this->assign('name',$name);
        $this->display();
    }
    
    
    public function monitor_index(){
        //$demo = M('zz_port');
        $name= trim($_REQUEST['name']);
        if (empty($name)) {
            //$where['port_id'] != null;
            $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
            $num = $Model->query("select count(*) as num from (SELECT DISTINCT(name) as name,code FROM vnet_zz_port WHERE port_id is not null) as a;");
            $count = intval($num[0]['num']);
            //$test_data->Distinct(true)->field('descriprion')->where($where)->select();
            $page = new \Think\Page($count,15);
            $p = $page->firstRow;
            $e = $page->listRows;
            $list= $Model->query("SELECT DISTINCT(name) as name,code FROM vnet_zz_port WHERE port_id is not null limit $p,$e;");
        }else{
            
            $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
            $num = $Model->query("select count(*) as num from (SELECT DISTINCT(name) as name,code FROM vnet_zz_port WHERE port_id is not null and name like '%$name%') as a;");
            $count = intval($num[0]['num']);
            //$test_data->Distinct(true)->field('descriprion')->where($where)->select();
            $page = new \Think\Page($count,15);
            $p = $page->firstRow;
            $e = $page->listRows;
            $list= $Model->query("SELECT DISTINCT(name) as name,code FROM vnet_zz_port WHERE port_id is not null and name like '%$name%' limit $p,$e;");
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
    
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('url_flag','customer_index');
        $this->assign('name',$name);
        $this->display();
    }
    
    
    public function customer_detail(){
        $code = $_REQUEST['code'];
        $cust = M('zz_company');
        $person = M('zz_person');
        $ip = M('zz_ip');
        $port = M('zz_port');
        
        $where['code'] = $code;
        $cust_info = $cust->where($where)->find(); //客户信息
        $person_count = $person->where($where)->count();// 接口人数
        
        $ip_count = $ip->where($where)->count();//ip数
        
        $port_count = $port->where($where)->count();//端口数
        
        $port_list = $port->where($where)->select();//端口数
        $i = 0;
        
        
        foreach ($port_list as $temp){
            $one = $temp['bandwidth'];
            
            if (strpos($one, "无限速") || strpos($one, "不限速")) {
                $i = "无限速";break;
            }
            
            if (empty($one) || $one == "") {
                $i += 0;
            }
            
            if (strpos($one, "G")) {
                if(preg_match("/(\d+)\.(\d+)/is", $one, $match)){
                    $i += (1000*$match[0]);
                }elseif(preg_match("/(\d+)/", $one, $matchs)){
                    $i += (1000*$matchs[0]);
                }else{
                    $i += 0;
                }
            }elseif(strpos($one, "M")) {
                if(preg_match("/(\d+)\.(\d+)/is", $one, $match)){
                    $i += $match[0];
                }elseif(preg_match("/(\d+)/", $one, $matchs)){
                    $i += $matchs[0];
                }else{
                    $i += 0;
                }
            }else{
                if(preg_match("/(\d+)\.(\d+)/is", $one, $match)){
                    $i += $match[0];
                }elseif(preg_match("/(\d+)/", $one, $matchs)){
                    $i += $matchs[0];
                }else{
                    $i += 0;
                }
            }
            
        }
        
        
        if ($i == "无限速") {
            $band = "无限速";
        }elseif ($i == 0){
            $band = "0 M";
        }else{
            $band = "$i M";
        }
        
        $this->assign('cust_info',$cust_info);
        $this->assign('port',$port_count." 个");
        $this->assign('ip',$ip_count." 段");
        $this->assign('person',$person_count." 个");
        $this->assign('bandwidth',$band);
        $this->assign('code',$cust_info['code']);
        $this->assign('url_flag','customer_index');
        $this->display();
        
    }
    
    
    public function customer_contract(){
        $demo = M('zz_contract');
        $name= $_REQUEST['name'];
        if (empty($name)) {
            $count = $demo->count();
            $page = new \Think\Page($count,15);
            $list=$demo->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $where['name'] = array('like',"%$name%");
            $count = $demo->where($where)->count();
            $page = new \Think\Page($count,15);
            $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
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
    
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('url_flag','customer_contract');
        $this->assign('name',$name);
        $this->display();
    }
    
    public function customer_zichan(){
        $demo = M('zz_port');
        $wl_device_name= $_REQUEST['wl_device_name'];
        $code = $_REQUEST['code'];
        
        $port_all = M("traffic_portlist")->select();
        $port_arr = deal_portid_portname($port_all);
        
        if (empty($wl_device_name)) {
            $where['code'] = $code;
            $count = $demo->where($where)->count();
            $page = new \Think\Page($count,15);
            $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $where['wl_device_name'] = array('like',"%$wl_device_name%");
            $count = $demo->where($where)->count();
            $page = new \Think\Page($count,15);
            $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }
    
        $map['wl_device_name'] = $_GET['wl_device_name'];
        foreach($map as $key=>$val) {
            $p->parameter .= "$key=".urlencode($val)."&";
        }
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        $show = $page->show();
    
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('url_flag','customer_index');
        $this->assign('wl_device_name',$wl_device_name);
        $this->assign('code',$code);
        $this->assign('port_arr',$port_arr);
        $this->display();
    }
    
    
    public function customer_ip_zichan(){
        $demo = M('zz_ip');
        $name= $_REQUEST['name'];
        $code = $_REQUEST['code'];
    
        if (empty($name)) {
            $where['code'] = $code;
            $count = $demo->where($where)->count();
            $page = new \Think\Page($count,15);
            $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $where['name'] = array('like',"%$name%");
            $count = $demo->where($where)->count();
            $page = new \Think\Page($count,15);
            $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
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
    
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('url_flag','customer_index');
        $this->assign('name',$name);
        $this->assign('code',$code);
        $this->display();
    }
    
    
    public function customer_person_zichan(){
        $demo = M('zz_person');
        $name= $_REQUEST['name'];
        $code = $_REQUEST['code'];
    
        if (empty($name)) {
            $where['code'] = $code;
            $count = $demo->where($where)->count();
            $page = new \Think\Page($count,15);
            $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $where['name'] = array('like',"%$name%");
            $count = $demo->where($where)->count();
            $page = new \Think\Page($count,15);
            $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
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
    
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('url_flag','customer_index');
        $this->assign('name',$name);
        $this->assign('code',$code);
        $this->display();
    }
    
    
   public function port_monitor(){
       
       $id = $_REQUEST['id'];
       $port_info = M('zz_port')->where("id = $id")->find();
       $n = $port_info['code'];
       if (IS_POST) {
           $editdata['port_id'] = $_REQUEST['port_id'];
           $port_id = $_REQUEST['port_id'];
           $port_find = M('zz_port')->where("port_id = $port_id")->find();
           /*if ($port_find) {
               $this->error("端口已被占用");die;
           }*/
           
           $res = M('zz_port')->where('id="'.$id.'"')->setField($editdata);
           if ($res) {
               //$url = U('Customer/customer_zichan?code='.$n);
               $this->success("更新成功");die;
           }else{
               //$url = U('Customer/customer_zichan?code='.$n);
               $this->error("更新失败");die;
           }
       }
       
       
       
       $device = trim($port_info['wl_device_name']);
       if (empty($device)) {
           $this->error("网络设备名称不能为空");die;
       }else{
           $n = trim($device);
           $finds = M('ipdb_items')->where("common_name ='$device'")->find();
           if ($finds) {
               if ($finds['hostid']) {
                   $hostid = $finds['hostid'];
                   $company_name = $port_info['name'];
                   $wl_device_name = $port_info['wl_device_name'];
                   $port_desc = $port_info['port_desc'];
                   $where['hostid'] = $hostid;
                   $list = M('traffic_portlist')->where($where)->select();
                   
                   $this->assign('company_name',$company_name);
                   $this->assign('wl_device_name',$wl_device_name);
                   $this->assign('port_desc',$port_desc);
                   $this->assign('port_list',$list);
               }else{
                   $url = U('Item/index?p=1&common_name='.$n);
                   $this->error("请添加监控",$url);die;
               }
           }else{
               $this->error("网络设备名称在数据库中不存在，请管理员补录");die;
           }
       }
       
       
       
       $this->assign('url_flag','customer_index');
       $this->assign("id",$id);
       $this->display();
   }
   
   
   public function device_find(){
       $wl_device_name = trim($_REQUEST['wl_device_name']);
       
       if (empty($wl_device_name)) {
           $this->error("网络设备名称不能为空");die;
       }else{
           $n = trim($wl_device_name);
           $finds = M('ipdb_items')->where("common_name ='$wl_device_name'")->find();
           if ($finds) {
               if ($finds['hostid']) {
                   $hostid = $finds['hostid'];
                   $this->redirect('Item/view_monitor', array('id' => $finds['id']), 0, '页面跳转中...');
               }else{
                   $url = U('Item/index?p=1&common_name='.$n);
                   $this->error("请添加监控",$url);die;
               }
           }else{
               $this->error("网络设备名称在数据库中不存在，请管理员补录");die;
           }
       }
       
   }
   
   
   public function  device_list(){
   
       $Ipdb_locations =M("ipdb_locations");
       $Ipdb_locareas =M("ipdb_locareas");
       $Ipdb_racks =M("ipdb_racks");
       $Ipdb_items =M("ipdb_items");
       
       $rackid= $_REQUEST['rackid'];
       $location_id= $_REQUEST['location_id'];
       $depart = $_REQUEST['depart'];
       
       $lids=implode(',',$location_id);
       //---------------------获取动态机房，房间数据
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
           if (!empty($depart)) {
               $where['depart_id'] = $depart;
           }
           
           $where['itemtypeid'] = 1;
           //$count = $Ipdb_items->where($where)->count();
           //$page = new \Think\Page($count,30);
           $list = $Ipdb_items->where($where)->order('id desc')->select();
       }else{
           if (!empty($depart)) {
               $where['depart_id'] = $depart;
           }
           
           $where['itemtypeid'] = 1;
           $where['locationid'] = array('in',$location_id);
          // $count = $Ipdb_items->where($where)->count();
         //  $page = new \Think\Page($count,30);
           $list = $Ipdb_items->where($where)->order('id desc')->select();
       }
       
       
       /*foreach($map as $key=>$val) {
           $p->parameter .= "$key=".urlencode($val)."&";
       }
       $page->setConfig('header','共');
       $page->setConfig('first','«');
       $page->setConfig('last','»');
       //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
       $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
       $show = $page->show();*/
       
       //var_dump($list);die;
       /*$showmenu=array();
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
   
       }*/
       
       $depart_list_now = M('department')->select();
       $depart_arr_now = deal_array($depart_list_now, 'id', 'depart_name');
       
       
       $location_list = M('ipdb_locations')->select();
       $area_list = M('ipdb_locareas')->select();
       $rack_list = M('ipdb_racks')->select();
       
       $location_list = deal_array($location_list, 'id', 'name');
       $area_list = deal_array($area_list, 'id', 'areaname');
       $rack_list = deal_array($rack_list, 'id', 'name');
       
       $this->assign('location_list',$location_list);
       $this->assign('area_list',$area_list);
       $this->assign('rack_list',$rack_list);
       $this->assign('depart_list',$depart_list_now);
       $this->assign('depart',$depart);
       $this->assign('depart_arr',$depart_arr_now);
       
       $this->assign('page',$show);
       $this->assign('url_flag','device_list');
       $this->assign('json_showmenu',$json_showmenu);
       $this->assign('tr',$tr);
       $this->assign('list',$list);
       $this->assign('showlabel',$showlabel);
       $this->display();
   }
   
   
   public function alert(){
       $demo = M('cust_alert');
    
       $max_find = $demo->field("max(version) as version")->select();
       $version = $max_find[0]['version'];
       $version = intval($version) - 1;
       /*if (empty($name)) {
           $count = $demo->count();
           $page = new \Think\Page($count,15);
           $list=$demo->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
       }else{
           $where['name'] = array('like',"%$name%");
           $count = $demo->where($where)->count();
           $page = new \Think\Page($count,15);
           $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
       }*/
       $count = $demo->where("version = '$version' and depart_id = 5 and portname not like 'unrouted%' and beyond > 1048576")->count();
       $page = new \Think\Page($count,15);
       $list=$demo->where("version = '$version' and depart_id = 5 and portname not like 'unrouted%' and beyond > 1048576")->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
   
       
       $page->setConfig('header','共');
       $page->setConfig('first','«');
       $page->setConfig('last','»');
       //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
       $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
       $show = $page->show();
   
       $this->assign('page',$show);
       $this->assign('list',$list);
       $this->assign('url_flag','customer_alert');
       //$this->assign('name',$name);
       $this->display();
   }
   
   
   public function alert_huadong(){
       $demo = M('cust_alert');
   
       $max_find = $demo->field("max(version) as version")->select();
       $version = $max_find[0]['version'];
       $version = intval($version) - 1;
       /*if (empty($name)) {
        $count = $demo->count();
        $page = new \Think\Page($count,15);
        $list=$demo->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
        $where['name'] = array('like',"%$name%");
        $count = $demo->where($where)->count();
        $page = new \Think\Page($count,15);
        $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
       }*/
       $count = $demo->where("version = '$version' and depart_id = 4 and portname not like 'unrouted%' and beyond > 1048576")->count();
       $page = new \Think\Page($count,15);
       $list=$demo->where("version = '$version' and depart_id = 4 and portname not like 'unrouted%' and beyond > 1048576")->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        
        
       $page->setConfig('header','共');
       $page->setConfig('first','«');
       $page->setConfig('last','»');
       //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
       $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
       $show = $page->show();
        
       $this->assign('page',$show);
       $this->assign('list',$list);
       $this->assign('url_flag','customer_alert');
       //$this->assign('name',$name);
       $this->display();
   }
   
   public function alert_huanan(){
       $demo = M('cust_alert');
        
       $max_find = $demo->field("max(version) as version")->select();
       $version = $max_find[0]['version'];
       $version = intval($version) - 1;
       /*if (empty($name)) {
        $count = $demo->count();
        $page = new \Think\Page($count,15);
        $list=$demo->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
        $where['name'] = array('like',"%$name%");
        $count = $demo->where($where)->count();
        $page = new \Think\Page($count,15);
        $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
       }*/
       $count = $demo->where("version = '$version' and depart_id = 6 and portname not like 'unrouted%' and beyond > 1048576")->count();
       $page = new \Think\Page($count,15);
       $list=$demo->where("version = '$version' and depart_id = 6 and portname not like 'unrouted%' and beyond > 1048576")->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
   
   
       $page->setConfig('header','共');
       $page->setConfig('first','«');
       $page->setConfig('last','»');
       //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
       $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
       $show = $page->show();
   
       $this->assign('page',$show);
       $this->assign('list',$list);
       $this->assign('url_flag','customer_alert');
       //$this->assign('name',$name);
       $this->display();
   }
   
   public function alert_chuanshu(){
       $demo = M('cust_alert');
   
       $max_find = $demo->field("max(version) as version")->select();
       $version = $max_find[0]['version'];
       $version = intval($version) - 1;
       /*if (empty($name)) {
        $count = $demo->count();
        $page = new \Think\Page($count,15);
        $list=$demo->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
        $where['name'] = array('like',"%$name%");
        $count = $demo->where($where)->count();
        $page = new \Think\Page($count,15);
        $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
       }*/
       $count = $demo->where("version = '$version' and depart_id = 8")->count();
       $page = new \Think\Page($count,15);
       $list=$demo->where("version = '$version' and depart_id = 8")->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        
        
       $page->setConfig('header','共');
       $page->setConfig('first','«');
       $page->setConfig('last','»');
       //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
       $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
       $show = $page->show();
        
       $this->assign('page',$show);
       $this->assign('list',$list);
       $this->assign('url_flag','customer_alert');
       //$this->assign('name',$name);
       $this->display();
   }
   
   
   public function alert_guke(){
       $demo = M('cust_alert');
       
       $max_find = $demo->field("max(version) as version")->select();
       $version = $max_find[0]['version'];
       $version = intval($version) - 1;
       /*if (empty($name)) {
        $count = $demo->count();
        $page = new \Think\Page($count,15);
        $list=$demo->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
        $where['name'] = array('like',"%$name%");
        $count = $demo->where($where)->count();
        $page = new \Think\Page($count,15);
        $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
       }*/
       $count = $demo->where("version = '$version' and depart_id = 5 and cust is not null")->count();
       $page = new \Think\Page($count,15);
       $list=$demo->where("version = '$version' and depart_id = 5 and cust is not null")->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        
        
       $page->setConfig('header','共');
       $page->setConfig('first','«');
       $page->setConfig('last','»');
       //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
       $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
       $show = $page->show();
        
       $this->assign('page',$show);
       $this->assign('list',$list);
       $this->assign('url_flag','customer_alert');
       //$this->assign('name',$name);
       $this->display();
   }
   
   
   public function alert_huadong_guke(){
       $demo = M('cust_alert');
        
       $max_find = $demo->field("max(version) as version")->select();
       $version = $max_find[0]['version'];
       $version = intval($version) - 1;
       /*if (empty($name)) {
        $count = $demo->count();
        $page = new \Think\Page($count,15);
        $list=$demo->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
        $where['name'] = array('like',"%$name%");
        $count = $demo->where($where)->count();
        $page = new \Think\Page($count,15);
        $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
       }*/
       $count = $demo->where("version = '$version' and depart_id = 4 and cust is not null")->count();
       $page = new \Think\Page($count,15);
       $list=$demo->where("version = '$version' and depart_id = 4 and cust is not null")->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
   
   
       $page->setConfig('header','共');
       $page->setConfig('first','«');
       $page->setConfig('last','»');
       //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
       $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
       $show = $page->show();
   
       $this->assign('page',$show);
       $this->assign('list',$list);
       $this->assign('url_flag','customer_alert');
       //$this->assign('name',$name);
       $this->display();
   }
   
   public function alert_huanan_guke(){
       $demo = M('cust_alert');
   
       $max_find = $demo->field("max(version) as version")->select();
       $version = $max_find[0]['version'];
       $version = intval($version) - 1;
       /*if (empty($name)) {
        $count = $demo->count();
        $page = new \Think\Page($count,15);
        $list=$demo->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
        $where['name'] = array('like',"%$name%");
        $count = $demo->where($where)->count();
        $page = new \Think\Page($count,15);
        $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
       }*/
       $count = $demo->where("version = '$version' and depart_id = 6")->count();
       $page = new \Think\Page($count,15);
       $list=$demo->where("version = '$version' and depart_id = 6")->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        
        
       $page->setConfig('header','共');
       $page->setConfig('first','«');
       $page->setConfig('last','»');
       //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
       $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
       $show = $page->show();
        
       $this->assign('page',$show);
       $this->assign('list',$list);
       $this->assign('url_flag','customer_alert');
       //$this->assign('name',$name);
       $this->display();
   }
   
   public function chuanshu_index(){
       //传输_index
       $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表;
       
       $name= trim($_REQUEST['name']);
       if (empty($name)) {
           $count = $Model->query("SELECT count(*) as num FROM (SELECT DISTINCT(code) as code FROM vnet_zz_port where is_cs = 1) as na");
           $num = $count[0]['num'];
           $page = new \Think\Page($num,15);
           $pre = $page->firstRow;
           $allow = $page->listRows;
           $list=  $Model->query("SELECT DISTINCT(code) as code,name FROM vnet_zz_port where is_cs = 1 limit $pre,$allow");
       }else{
           $count = $Model->query("SELECT count(*) as num FROM (SELECT DISTINCT(code) as code FROM vnet_zz_port where name like '%$name%' and is_cs = 1) as na");
           $num = $count[0]['num'];
           $page = new \Think\Page($num,15);
           $pre = $page->firstRow;
           $allow = $page->listRows;
           $list=  $Model->query("SELECT DISTINCT(code) as code,name FROM vnet_zz_port where name like '%$name%' and is_cs = 1 limit $pre,$allow");
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
       
       $this->assign('page',$show);
       $this->assign('list',$list);
       $this->assign('url_flag','customer_index');
       $this->assign('name',$name);
       $this->display();
   }
   
   public function chuanshu_customer_detail(){
       $code = $_REQUEST['code'];
       $cust = M('zz_company');
       $person = M('zz_person');
       $ip = M('zz_ip');
       $port = M('zz_port');
   
       $where['code'] = $code;
       $cust_info = $port->where($where)->find(); //客户信息
       $person_count = $person->where($where)->count();// 接口人数
   
       $ip_count = $ip->where($where)->count();//ip数
   
       $port_count = $port->where($where)->count();//端口数
   
       $port_list = $port->where($where)->select();//端口数
       $i = 0;
   
   
       foreach ($port_list as $temp){
           $one = $temp['bandwidth'];
   
           if (strpos($one, "无限速") || strpos($one, "不限速")) {
               $i = "无限速";break;
           }
   
           if (empty($one) || $one == "") {
               $i += 0;
           }
   
           if (strpos($one, "G")) {
               if(preg_match("/(\d+)\.(\d+)/is", $one, $match)){
                   $i += (1000*$match[0]);
               }elseif(preg_match("/(\d+)/", $one, $matchs)){
                   $i += (1000*$matchs[0]);
               }else{
                   $i += 0;
               }
           }elseif(strpos($one, "M")) {
               if(preg_match("/(\d+)\.(\d+)/is", $one, $match)){
                   $i += $match[0];
               }elseif(preg_match("/(\d+)/", $one, $matchs)){
                   $i += $matchs[0];
               }else{
                   $i += 0;
               }
           }else{
               if(preg_match("/(\d+)\.(\d+)/is", $one, $match)){
                   $i += $match[0];
               }elseif(preg_match("/(\d+)/", $one, $matchs)){
                   $i += $matchs[0];
               }else{
                   $i += 0;
               }
           }
   
       }
   
   
       if ($i == "无限速") {
           $band = "无限速";
       }elseif ($i == 0){
           $band = "0 M";
       }else{
           $band = "$i M";
       }
   
       $this->assign('cust_info',$cust_info);
       $this->assign('port',$port_count." 个");
       $this->assign('ip',$ip_count." 段");
       $this->assign('person',$person_count." 个");
       $this->assign('bandwidth',$band);
       $this->assign('code',$cust_info['code']);
       $this->assign('url_flag','customer_index');
       $this->display();
   
   }
   
    

}
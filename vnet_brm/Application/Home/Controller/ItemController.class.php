<?php
namespace Home\Controller;
use Think\Controller;
class  ItemController extends HomeController {
    
    public function monitor(){
        $id = $_REQUEST['id'];
        $iteminfo = M('ipdb_items')->where("id = $id")->find();
        $ipv4 = trim($iteminfo['ipv4']);
        $name =  $iteminfo['common_name'];
        $itemtypeid = $iteminfo['itemtypeid'];
        $hostid = $iteminfo['hostid'];
        $depart_id = $iteminfo['depart_id'];
        
        $depart = M('department')->select();
        $snmp_list = deal_array($depart, "id", "snmps");
        $snmp=$snmp_list[$depart_id];
        
        if (empty($hostid) && !empty($snmp)) {
             import("Org.Util.ZabbixApiAbstract");
            import("Org.Util.ZabbixApi");
            $api = new \ZabbixApi("http://211.151.5.46/zabbix/api_jsonrpc.php", "Admin", "buzhidao");

             // $host_add= $api->hostCreate($params=array("host"=>"$name-zichan","name"=>"$name-zichan","interfaces"=>array(array("type"=>2, "main"=>1, "useip"=>1, "ip"=>$ipv4, "dns"=>"", "port"=>"161")),"proxy_hostid"=>"10088","groups"=>array(array("groupid"=>37)),"macros"=>array(array("macro"=>'{$SNMP_COMMUNITY}',"value"=>$snmp)),"templates"=>array(array("templateid"=>11229))));
           $a=array("10492","10673","10672");
           $random_keys=array_rand($a,1);
           $proxyid=$a[$random_keys];

 
             $host_add= $api->hostCreate($params=array("host"=>"$name-zichan","name"=>"$name-zichan","interfaces"=>array(array("type"=>2, "main"=>1, "useip"=>1, "ip"=>"$ipv4", "dns"=>"", "port"=>"161")),"proxy_hostid"=>"$proxyid","groups"=>array(array("groupid"=>12)),"macros"=>array(array("macro"=>'{$SNMP_COMMUNITY}',"value"=>$snmp)),"templates"=>array(array("templateid"=>10671))));

             $hostcc =$host_add->hostids;
             $hostid = $hostcc[0];
              $loss= M('ipdb_items')->where('id="'.$id.'"  ')->setField(array('hostid'=>$hostid));
              
              //$snmp = "WG-21vianet-RO";
            $a = system("php /var/www/html/Traffi/walk.php $snmp $ipv4 $hostid");

           $this->show('<script type="text/javascript" >alert("监控成功");history.go(-1);location.reload(); </script>');die;

         }else{
             $this->show('<script type="text/javascript" >alert("监控添加失败");history.go(-1);location.reload();</script>');die;
         }

    }
    
    
    public function index(){
        
        /**如果为超级管理员**/
        if (IS_ROOT) {
            $itemType = M('itemtype');
                $Agents = M('agents');
                
                $type_list = $itemType->select();
                $depart_list = M('department')->select();
                $agent_list = $Agents->select();
                $location_list = M('ipdb_locations')->select();
                $area_list = M('ipdb_locareas')->select();
                $rack_list = M('ipdb_racks')->select();
                
                
                $location_list = deal_array($location_list, 'id', 'name');
                $area_list = deal_array($area_list, 'id', 'areaname');
                $rack_list = deal_array($rack_list, 'id', 'name');
                $type_list = deal_array($type_list,'id','typedesc');
                $agent_list = deal_array($agent_list,'id','title');
                $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
                
                $items = M('ipdb_items');
                
                
                $condition = array();
                $condition['rackid'] = array('neq',0); 
                if ($_GET['itemtypeid']) {
                    $condition['itemtypeid'] = $_GET['itemtypeid'];
                }
                if ($_GET['depart_id']) {
                    $condition['depart_id'] = $_GET['depart_id'];
                }
                if ($_GET['sn']) {
                    $condition['sn'] = array('like',"%".$_GET['sn']."%");
                }
                if ($_GET['ip']) {
                    $condition['ipv4'] = array('like',"%".$_GET['ip']."%");
                }
                if ($_GET['common_name']) {
                    $condition['common_name'] = array('like',"%".$_GET['common_name']."%");
                }
                
                $count = $items->where($condition)->count();
                $page = new \Think\Page($count,15);
                $list=$items->order('id desc')->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
                
                $map['itemtypeid'] = $_GET['itemtypeid'];
                $map['sn'] = $_GET['sn'];
                $map['depart_id'] = $_GET['depart_id'];
                $map['common_name'] = $_GET['common_name'];
                foreach($map as $key=>$val) {
                    $p->parameter .= "$key=".urlencode($val)."&";
                }
                $page->setConfig('header','共');
                $page->setConfig('first','«');
                $page->setConfig('last','»');
                //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
                $show = $page->show();
                
                $type_all_list = $itemType->select();
                
                $this->assign('location_list',$location_list);
                $this->assign('area_list',$area_list);
                $this->assign('rack_list',$rack_list);
                $this->assign('state_info',$state_info);
                $this->assign('type_list',$type_list);
                $this->assign('type_all_list',$type_all_list);
                $this->assign('agent_list',$agent_list);
                $this->assign('depart_list',$depart_list);
                $this->assign('depart_arr_now',$depart_arr_now);
                $this->assign('list',$list);
                $this->assign('page',$show);
                $this->assign('url_flag','item_index');
                $this->display();
        }else{
            
            $uid = is_login();
            $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
            
            $itemType = M('itemtype');
            $Agents = M('agents');
            
            $type_list = $itemType->select();
                $depart_list = M('department')->select();
                $agent_list = $Agents->select();
                $location_list = M('ipdb_locations')->select();
                $area_list = M('ipdb_locareas')->select();
                $rack_list = M('ipdb_racks')->select();
                
                
                $location_list = deal_array($location_list, 'id', 'name');
                $area_list = deal_array($area_list, 'id', 'areaname');
                $rack_list = deal_array($rack_list, 'id', 'name');
                $type_list = deal_array($type_list,'id','typedesc');
                $agent_list = deal_array($agent_list,'id','title');
                $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
            
            
            if (empty($arr)) {
                
            }else{
                //$map
                $map_depart['id'] = array('in',$arr);
                $depart_list = M('department')->where($map_depart)->select();
                $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
                
                $items = M('ipdb_items');
                
                $condition = array();
                $condition['depart_id'] = array('in',$arr);
                $condition['rackid'] = array('neq',0);
                if ($_GET['itemtypeid']) {
                    $condition['itemtypeid'] = $_GET['itemtypeid'];
                }
                if ($_GET['depart_id']) {
                    $condition['depart_id'] = $_GET['depart_id'];
                }
                if ($_GET['sn']) {
                    $condition['sn'] = array('like',"%".$_GET['sn']."%");
                }
                if ($_GET['ip']) {
                    $condition['ipv4'] = array('like',"%".$_GET['ip']."%");
                }
                if ($_GET['common_name']) {
                    $condition['common_name'] = array('like',"%".$_GET['common_name']."%");
                }
                
                $count = $items->where($condition)->count();
                $page = new \Think\Page($count,15);
                $list=$items->order('id desc')->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
                
                $map['itemtypeid'] = $_GET['itemtypeid'];
                $map['sn'] = $_GET['sn'];
                $map['depart_id'] = $_GET['depart_id'];
                $map['common_name'] = $_GET['common_name'];
                foreach($map as $key=>$val) {
                    $p->parameter .= "$key=".urlencode($val)."&";
                }
                $page->setConfig('header','共');
                $page->setConfig('first','«');
                $page->setConfig('last','»');
                //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
                $show = $page->show();
                
                $type_all_list = $itemType->select();
                
                $this->assign('location_list',$location_list);
                $this->assign('area_list',$area_list);
                $this->assign('rack_list',$rack_list);
                $this->assign('type_list',$type_list);
                $this->assign('type_all_list',$type_all_list);
                $this->assign('agent_list',$agent_list);
                $this->assign('depart_list',$depart_list);
                $this->assign('depart_arr_now',$depart_arr_now);
                $this->assign('list',$list);
                $this->assign('page',$show);
            }
            
            
            
            $this->assign('url_flag','item_index');
            $this->display();
            
            
        }
        
        
        
    }
    
    public function item_new() {
        $Agents = M('agents');
        $agenttag = 3;
        $itemType = M('itemtype');
        $Items = M('ipdb_items');
        
        
        
        if ( IS_POST ) {
            $itemtypeid= I('post.itemtypeid');
            $manufacturerid= I('post.manufacturerid');
            $common_name= I('post.common_name');
            $model= I('post.model');
            $sn= I('post.sn');
            $asset= I('post.asset');
            $usize= I('post.usize');
            $comments= I('post.comments');
            
            $condition['is_store'] = 1;
            $location_info  =  M('ipdb_locations')->where($condition)->find();
            $location_id = 1751;
            $locareaid = 1752;
            $rackid = 1753;

            
            $origin= I('post.origin');
            $purchprice= I('post.purchprice');
            $purchasedate= I('post.purchasedate');
            $warrantymonths= I('post.warrantymonths');
            $ram= I('post.ram');
            $cpu= I('post.cpu');
            
            $dnsname= I('post.dnsname');
            $macs= I('post.macs');
            $ipv4= I('post.ipv4');
            $ipv6= I('post.ipv6');
            $switchid= I('post.switchid');
            $ports= I('post.ports');
            
            $sn2= I('post.sn2');
            $depart_id= I('post.depart_id');
            $belong_product= I('post.belong_product');
            $slot_number= I('post.slot_number');
            $power= I('post.power');
            
             
            $data['itemtypeid'] = $itemtypeid;
            $data['manufacturerid'] = $manufacturerid;
            $data['common_name'] = $common_name;
            $data['model'] = $model;
            $data['sn'] = $sn;
            $data['asset'] = $asset;
            $data['usize'] = $usize;
            $data['comments'] = $comments;
            
            $data['origin'] = $origin;
            $data['purchprice'] = $purchprice;
            $data['purchasedate'] =  strtotime($purchasedate);
            $data['warrantymonths'] = $warrantymonths;
            $data['ram'] = $ram;
            $data['cpu'] = $cpu;
            
            $data['dnsname'] = $dnsname;
            $data['macs'] = $macs;
            $data['ipv4'] =  $ipv4;
            $data['ipv6'] = $ipv6;
            $data['switchid'] = $switchid;
            $data['ports'] = $ports;
            
            $data['status'] = 2; //默认入库
            $data['rackid'] = $rackid;
            $data['locareaid'] = $locareaid;
            $data['locationid'] = $location_id;
            
            $data['sn2'] = $sn2;
            $data['depart_id'] =  $depart_id;
            $data['belong_product'] = $belong_product;
            $data['slot_number'] = $slot_number;
            $data['power'] = $power;
    
            empty($sn) && $this->error('请输入sn号:');
    
            $checklist=$Items->where('sn = "'.$sn.'" ')->select();
            if (empty($checklist)){
                $list=$Items->data($data)->add();
                if (!empty($list)){
                    $this->show('<script type="text/javascript" >alert("硬件添加成功");window.location.href="{:U("Item/index")}"; </script>');
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("硬件重复添加，请重新添加");window.location.href="{:U("Item/item_new")}"; </script>');
            }
        }
        
        $type_list = $itemType->select();
        $agent_list = $Agents->where(" type = 3 ")->select();
        /**switch_list**/
        if (IS_ROOT) {
            $switch_list = $Items->where("itemtypeid = 1")->field('id,common_name')->select();
            $depart_list = M('department')->select();
        }else{
            
            $uid = is_login();
            $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
            $map['itemtypeid'] = 1;
            $map['depart_id'] = array('in',$arr);
            $switch_list = $Items->where($map)->field('id,common_name')->select();
            $map_depart['id'] = array('in',$arr);
            $depart_list = M('department')->where($map_depart)->select();
        }
        
        
        $this->assign('switch_list',$switch_list); //6部分传值
        $this->assign('depart_list',$depart_list); //6部分传值
        $this->assign('type_list',$type_list);
        $this->assign('agent_list',$agent_list);
        $this->assign('url_flag','item_index');
        $this->display();
    }
    
    
    public function item_edit() {
        $Agents = M('agents');
        $agenttag = 3;
        $itemType = M('itemtype');
        $Items = M('ipdb_items');
        $Softs = M('software');
        $contract = M('contracts');
        
        $id = $_REQUEST['id'];
        
        
        //首先获取item信息
        $item_info = $Items->where('id="'.$id.'"  ')->find();
        //判断权限
        if (!IS_ROOT) {
           if (IS_OWNER) {
               $depart_id_quanxian = $item_info['depart_id'];
               $uid_quanxian = $_SESSION['user_auth']['uid'];
               $quanxian_arr = get_hostgroup_by_uid($uid_quanxian);
               if (!in_array($depart_id_quanxian, $quanxian_arr)) {
                   $this->show('<script type="text/javascript" >alert("非本部门资产不能查看");window.history.back(-1); </script>');die;
               }
           }else{
               $this->show('<script type="text/javascript" >alert("普通用户不能查看");window.history.back(-1); </script>');die;
           }
        }
        
        
        $flag = $_REQUEST['flag'];
        if ( IS_POST ) {


             if ($flag == '1') {
                 $itemtypeid = I('post.itemtypeid');
                 $manufacturerid = I('post.manufacturerid');
                 $common_name = I('post.common_name');
                 $model = I('post.model');
                 $sn = I('post.sn');
                 $asset = I('post.asset');
                 $usize = I('post.usize');
                 $comments = I('post.comments');
                 
                 $origin= I('post.origin');
                 $purchprice= I('post.purchprice');
                 $purchasedate= strtotime(I('post.purchasedate'));
                 $warrantymonths= I('post.warrantymonths');
                 $ram= I('post.ram');
                 $cpu= I('post.cpu');
                 
                 $dnsname= I('post.dnsname');
                 $macs= I('post.macs');
                 $ipv4= I('post.ipv4');
                 $ipv6= I('post.ipv6');
                 $switchid= I('post.switchid');
                 $ports= I('post.ports');
                 
                 $sn2= I('post.sn2');
                 $depart_id= I('post.depart_id');
                 $belong_product= I('post.belong_product');
                 $slot_number= I('post.slot_number');
                 $power= I('post.power');
                 
                 
                 $list=$Items->where('id="'.$id.'"  ')->setField(array('sn2'=>$sn2,'depart_id'=>$depart_id,'belong_product'=>$belong_product,'slot_number'=>$slot_number,'power'=>$power,'itemtypeid'=>$itemtypeid,'manufacturerid'=>$manufacturerid,'common_name'=>$common_name,'model'=>$model,'sn'=>$sn,'asset'=>$asset,'usize'=>$usize,'comments'=>$comments,'dnsname'=>$dnsname,'macs'=>$macs,'ipv4'=>$ipv4,'ipv6'=>$ipv6,'switchid'=>$switchid,'ports'=>$ports,'origin'=>$origin,'purchprice'=>$purchprice,'purchasedate'=>$purchasedate,'warrantymonths'=>$warrantymonths,'ram'=>$ram,'cpu'=>$cpu));
                 if ($list==1){
                     $url = U("Item/item_edit?id=".$id);

                     //var_dump($url);die;
                     history('ITEM',$_SESSION['user_auth']['username'],$id,'更新硬件基本信息');
                     $url = '<script type="text/javascript" >alert("信息修改成功");window.location.href="'.$url.'"; </script>';
                     $this->show($url);
                     die;
                 }
                 
             }else if($flag == '2'){
                 $status = I('post.status');
                 $list=$Items->where('id="'.$id.'"  ')->setField(array('status'=>$status));
                 if ($list==1){
                     $url = U("Item/item_edit?id=".$id);
                     history('ITEM',$_SESSION['user_auth']['username'],$id,'更新硬件状态信息');
                     $url = '<script type="text/javascript" >alert("状态修改成功");window.location.href="'.$url.'"; </script>';
                     $this->show($url);
                     die();
                 }
                 
             }else if($flag == '3'){
                 //硬件关联
                 $itemids = I('post.itemid');
                 $link = M('itemlink');
                 $del = $link->where("itemid1 = $id  or itemid2 = $id")->delete();
                 foreach ($itemids as $it){
                     $checklist=$link->where("itemid1 = $id and itemid2 = $it")->select();
                     if (empty($checklist)){
                         $data['itemid1'] = $id;
                         $data['itemid2'] = $it;
                         $ins= $link->data($data)->add();
                     }
                     
                 }
                 $url = U("Item/item_edit?id=".$id);
                 history('ITEM',$_SESSION['user_auth']['username'],$id,'更新关联硬件信息');
                 $url = '<script type="text/javascript" >alert("关联硬件修改成功");window.location.href="'.$url.'"; </script>';
                     $this->show($url);
                     die();
                 
             }else if($flag == '4'){
                 //合同关联
                 $contractids = I('post.contract_id');
                 $contract2item = M('contract2item');
                 $del = $contract2item->where("itemid = $id")->delete();
                 
                 foreach ($contractids as $contract_one){
                     $checklist=$contract2item->where("itemid = $id and contractid = $contract_one")->select();
                     if (empty($checklist)){
                         $data['itemid'] = $id;
                         $data['contractid'] = $contract_one;
                         $ins= $contract2item->data($data)->add();
                     }
                 }
                 $url = U("Item/item_edit?id=".$id);
                 history('ITEM',$_SESSION['user_auth']['username'],$id,'更新关联合同信息');
                 $url = '<script type="text/javascript" >alert("关联合同修改成功");window.location.href="'.$url.'"; </script>';
                 $this->show($url);
                 die();
             }else if($flag == '5'){
                 //软件关联
                 $softids = I('post.softid');
                 $item2soft = M('item2soft');
                 $del = $item2soft->where("itemid = $id")->delete();
                 
                 foreach ($softids as $softid_one){
                     $checklist=$item2soft->where("itemid = $id and softid = $softid_one")->select();
                     if (empty($checklist)){
                         $data['itemid'] = $id;
                         $data['softid'] = $softid_one;
                         $data['date'] = time();
                         $ins= $item2soft->data($data)->add();
                     }
                 }
                 $url = U("Item/item_edit?id=".$id);
                 history('ITEM',$_SESSION['user_auth']['username'],$id,'更新关联软件信息');
                 $url = '<script type="text/javascript" >alert("关联合同修改成功");window.location.href="'.$url.'"; </script>';
                 $this->show($url);
                 die();
             }else if($flag == '6'){
                 //其他数据
                 $dnsname= I('post.dnsname');
                 $macs= I('post.macs');
                 $ipv4= I('post.ipv4');
                 $ipv6= I('post.ipv6');
                 $switchid= I('post.switchid');
                 $ports= I('post.ports');
               
                 $list=$Items->where('id="'.$id.'"  ')->setField(array('dnsname'=>$dnsname,'macs'=>$macs,'ipv4'=>$ipv4,'ipv6'=>$ipv6,'switchid'=>$switchid,'ports'=>$ports));
                 if ($list==1){
                     $url = U("Item/item_edit?id=".$id);
                     history('ITEM',$_SESSION['user_auth']['username'],$id,'更新硬件其他信息');
                      $url = '<script type="text/javascript" >alert("其他信息修改成功");window.location.href="'.$url.'"; </script>';
                     $this->show($url);
                     die();
                 }
             }else if($flag == '7'){
                 if($_FILES['imageurl']['error'] == 0)
                 {
                     $upload = new \Think\Upload();// 实例化上传类
                     $upload->maxSize = 50 * 1024 * 1024 ; // 1M
                     $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                     $upload->rootPath = './upload/'; // 设置附件上传根目录
                     $upload->savePath = 'items/'; // 设置附件上传（子）目录
                     $upload->saveRule="";
                     // 上传文件
                     $info   =   $upload->upload();
                     if(!$info)
                     {
                         // 获取失败原因把错误信息保存到 模型的error属性中，然后在控制器里会调用$model->getError()获取到错误信息并由控制器打印
                         $this->error = $upload->getError();
                         return FALSE;
                     }
                     else
                     {
                         /**************** 生成缩略图 *****************/
                         // 先拼成原图上的路径
                 
                         $logo = $info['imageurl']['savepath'] . $info['imageurl']['savename'];
                          
                         /**************** 把路径放到表单中 *****************/
                          
                         $list=$Items->where('id="'.$id.'"  ')->setField(array('imageurl'=>$logo));
                         $url = U("Item/item_edit?id=".$id);
                         history('ITEM',$_SESSION['user_auth']['username'],$id,'图片软件信息');
                          $url = '<script type="text/javascript" >alert("图片修改成功");window.location.href="'.$url.'"; </script>';
                         $this->show($url);
                         die();
                     }
                 }
             }
             else{
                 
             }
           
        }
        
        
       
        $type_list = $itemType->select();
        $itemtypeidnow = $item_info['itemtypeid'];
        $soft_list = $Softs->where("itemtypeid = $itemtypeidnow")->select();
        $contract_list = $contract->select();
        //$log_list = M('history')->where('module_id="'.$id.'" and module = "ITEM" ')->select();
        
        
        
        $agent_list = $Agents->where(" type = 3 ")->select();
        
        
        /**所有设备**/
        if (IS_ROOT) {
            $count = $Items->where('id !="'.$id.'"  ')->count();
            $page = new \Think\Page($count,15);
            $item_list = $Items->where('id !="'.$id.'"  ')->limit($page->firstRow.','.$page->listRows)->select();  //3部分传值
            $page->setConfig('header','共');
            $page->setConfig('first','«');
            $page->setConfig('last','»');
            //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
            $show = $page->show();
        }else{
            $uid = is_login();
            $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
            $mapp['id'] != $id;
            $mapp['depart_id'] = array('in',$arr);
            $count = $Items->where($mapp)->count();
            $page = new \Think\Page($count,15);
            $item_list = $Items->where($mapp)->limit($page->firstRow.','.$page->listRows)->select();  //3部分传值
            $page->setConfig('header','共');
            $page->setConfig('first','«');
            $page->setConfig('last','»');
            //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
            $show = $page->show();
        }
        
        
        /**itemlink**/
        $itemlink_list = M('itemlink')->field('itemid1')->where("itemid2=$id")->union("select itemid2 from vnet_itemlink where itemid1=$id")->select();
        $itemlink_list_temp = array();
        if (!empty($itemlink_list)) {
            foreach ($itemlink_list as $vo){
                $itemlink_list_temp[] = $vo['itemid1'];
            }
        }
        /**contractlink**/
        $contractlink_list = M('contract2item')->field('contractid')->where("itemid=$id")->select();
        $contractlink_list_temp = array();
        if (!empty($contractlink_list)) {
            foreach ($contractlink_list as $vo){
                $contractlink_list_temp[] = $vo['contractid'];
            }
        }
        
        /**softlink**/
        $softlink_list = M('item2soft')->field('softid')->where("itemid=$id")->select();
        $softlink_list_temp = array();
        if (!empty($softlink_list)) {
            foreach ($softlink_list as $vo){
                $softlink_list_temp[] = $vo['softid'];
            }
        }
        
        
        /**switch_list**/
        if (IS_ROOT) {
            $switch_list = $Items->where("itemtypeid = 1")->field('id,common_name')->select();
            $depart_list = M('department')->select();
        }else{
            
            $uid = is_login();
            $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
            $map['itemtypeid'] = 1;
            $map['depart_id'] = array('in',$arr);
            $switch_list = $Items->where($map)->field('id,common_name')->select();
            $map_depart['id'] = array('in',$arr);
            $depart_list = M('department')->where($map_depart)->select();
        }

        $this->assign('id',$id);
        $this->assign('type_list',$type_list);
        $this->assign('agent_list',$agent_list);
        
        
        
        $this->assign('item_info',$item_info);
        $this->assign('depart_list',$depart_list);
        $this->assign('page',$show);
        $this->assign('item_list',$item_list); //3部分传值
        $this->assign('itemlink_list',$itemlink_list_temp); //3部分传值
        $this->assign('contract_list',$contract_list); //4部分传值
        $this->assign('contractlink_list',$contractlink_list_temp); //4部分传值
        $this->assign('soft_list',$soft_list);//5部分传值
        $this->assign('softlink_list',$softlink_list_temp);//5部分传值
        //$this->assign('log_list',$log_list); //7部分传值
        //$this->assign('log_list',$log_list); //7部分传值
        $this->assign('switch_list',$switch_list); //6部分传值
        $this->assign('url_flag','item_index');
        $this->display();
    }
    
    public function item_del(){
        $Items = M('ipdb_items');
        $id = $_REQUEST['id'];
        
        $where_item['id'] = $id;
        $one = $Items->where($where_item)->find();
        if (!empty($one['hostid'])) {
            $hostid_now = $one['hostid'];
            import("Org.Util.ZabbixApiAbstract");
            import("Org.Util.ZabbixApi");
            $api = new \ZabbixApi("http://211.151.5.46/zabbix/api_jsonrpc.php", "Admin", "buzhidao");
            $del_host=$api->hostDelete($params=array($hostid_now));
        }
        
        $del = $Items->where($where_item)->delete();
        if ($del) {
            
            $del_soft_link = M('item2soft')->where("itemid = $id")->delete();
            $del_item_link = M('itemlink')->where("itemid1 = $id or itemid2 = $id")->delete();
            $del_contract_link = M('contract2item')->where("itemid = $id")->delete();
            $del_tag_link = M('ipdb_tag2item')->where("itemid = $id")->delete();
            
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Item/index")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Item/index")}"; </script>');die;
        }
    }
    
    
    
    public function test(){
        $itemType = M('itemtype');
        $Agents = M('agents');
        
        $type_list = $itemType->select();
        
        $agent_list = $Agents->select();
        
        $type_list = deal_array($type_list,'id','typedesc');
        $agent_list = deal_array($agent_list,'id','title');
        
        $items = M('ipdb_items_test');
        
        
        $condition = array();
        if ($_GET['itemtypeid']) {
            $condition['itemtypeid'] = $_GET['itemtypeid'];
        }
        if ($_GET['sn']) {
            $condition['sn'] = array('like',"%".$_GET['sn']."%");
        }
        
        $count = $items->where($condition)->count();
        $page = new \Think\Page($count,15);
        $list=$items->order('id desc')->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
        
        $map['itemtypeid'] = $_GET['itemtypeid'];
        $map['sn'] = $_GET['sn'];
        foreach($map as $key=>$val) {
            $p->parameter .= "$key=".urlencode($val)."&";
        }
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        $show = $page->show();
        
        $type_all_list = $itemType->select();
        
        $this->assign('type_list',$type_list);
        $this->assign('type_all_list',$type_all_list);
        $this->assign('agent_list',$agent_list);
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->assign('url_flag','test');
        $this->display();
    }
    
    
    
    public function import_items(){
        $items = M('ipdb_items');
        $department = M('department');
        $itemtype = M('itemtype');
        $Agents = M('agents');
        
        if (IS_POST){
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('xlsx','xls');// 设置附件上传类型
            $upload->rootPath  =      './upload/'; // 设置附件上传根目录
            $upload->savePath = 'excels/'; // 设置附件上传（子）目录
            // 上传单个文件
            $info   =   $upload->uploadOne($_FILES['excelfile']);
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功 获取上传文件信息
                //echo $info['savepath'].$info['savename'];
                header('Content-type: text/html; charset=utf-8');
    
                import("Org.Util.PHPExcel");
                $PHPExcel=new \PHPExcel();
    
                if ($info['ext'] == "xls") {
                    
                    import("Org.Util.PHPExcel.Reader.Excel5");
    
                    $PHPReader=new \PHPExcel_Reader_Excel5();
    
                    $filename = "./upload/".$info['savepath'].$info['savename'];
                    //载入文件
                    $PHPExcel=$PHPReader->load($filename);
                    //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
                    $currentSheet=$PHPExcel->getSheet(0);
                    //获取总列数
                    $allColumn=$currentSheet->getHighestColumn();
                    //获取总行数
                    $allRow=$currentSheet->getHighestRow();
                    //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
                    for($currentRow=1;$currentRow<=$allRow;$currentRow++){
                        //从哪列开始，A表示第一列
                        if ($currentRow == 1) {
                            continue;
                        }
                        
                        for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                            //数据坐标
                            $address = $currentColumn.$currentRow;
                            $temp = $currentSheet->getCell($address)->getValue();
   
                            //读取到的数据，保存到数组$arr中
                            $arr[$currentRow][$currentColumn]=$temp;
                            if ($currentColumn == 'U' ) {
                                break;
                            }
                        }
    
                    }
                    //var_dump($arr[3]);die;
                    
                    $agent_list = $Agents->where(" type = 3 ")->select();
                    $type_list = $itemtype->select();
                    $department_list = $department->select();
                    $agent_list_now = deal_array_val_to_key($agent_list,'title','id');
                    $type_list_now = deal_array_val_to_key($type_list,'typedesc','id');
                    $department_list_now = deal_array_val_to_key($department_list,'depart_name','id');
                    
                    //处理数组
                    foreach ($arr as $v){
                        $data['common_name'] = $v['A'];
                        if ($v['B']!=null && $v['B']!="") {
                            $itemtype_key = $v['B'];
                            $data['itemtypeid'] = $type_list_now["$itemtype_key"];
                        }
                        $data['ipv4'] = $v['C'];
                        $data['usize'] = count_u_number($v['E']);
                        $data['sn'] = $v['J'];
                        $data['sn2'] = $v['K'];
                        $data['slot_number'] = $v['L'];
                        $data['belong_product'] = $v['O'];
                        $data['power'] = $v['P'];
                        if ($v['Q']!=null && $v['Q']!="") {
                            $anget_key = $v['Q'];
                            $data['manufacturerid'] = $agent_list_now{"$anget_key"};
                        }
                        $data['model'] = $v['R'];
                        if ($v['S']!=null && $v['S']!="") {
                            $depart_key = $v['S'];
                            $data['depart_id'] = $department_list_now{"$depart_key"};
                        }
                     
                        $comm = $v['A'];
                        $checklist=$items->where('common_name = "'.$comm.'" ')->field('id')->select();
                        if (empty($checklist)) {
                            $list=$items->data($data)->add();
                        }
                        
                    }
    
                   
                }else{
                    import("Org.Util.PHPExcel.Reader.Excel2007");
                    $PHPReader=new \PHPExcel_Reader_Excel2007();
    
                    $filename = "./upload/".$info['savepath'].$info['savename'];
                    //载入文件
                    $PHPExcel=$PHPReader->load($filename);
                    //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
                    $currentSheet=$PHPExcel->getSheet(0);
                    //获取总列数
                    $allColumn=$currentSheet->getHighestColumn();
                    //获取总行数
                    $allRow=$currentSheet->getHighestRow();
                    //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
                    for($currentRow=1;$currentRow<=$allRow;$currentRow++){
                        //从哪列开始，A表示第一列
                        if($currentRow == 1){
                            continue;
                        }
                        for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                            //数据坐标
                            $address = $currentColumn.$currentRow;
                            $temp = $currentSheet->getCell($address)->getValue();
                             
                            //读取到的数据，保存到数组$arr中
                            $arr[$currentRow][$currentColumn]=$temp;
                            if ($currentColumn == 'U' ) {
                                break;
                            }
    
                        }
    
                    }
                     
             
                    //EXCEL_200X_END
                }
                
                //INFO_IF_END
            }
            //IS_POST END
            
            
        }
    
        $this->assign('url_flag','test');
        $this->display();
    }
    
    public function item_info(){
        
        $id = $_REQUEST['id'];
        $itemType = M('itemtype');
        $Items = M('ipdb_items');
        $Softs = M('software');
        $contract = M('contracts');
        
        //首先获取item信息
        $item_info = $Items->where('id="'.$id.'"  ')->find();
        
        //判断权限
        if (!IS_ROOT) {
            if (IS_OWNER) {
                $depart_id_quanxian = $item_info['depart_id'];
                $departde = M('department')->where("id = $depart_id_quanxian")->find();
                $depart_name = $departde['depart_name'];
                $uid_quanxian = $_SESSION['user_auth']['uid'];
                $quanxian_arr = get_hostgroup_by_uid($uid_quanxian);
                if (!in_array($depart_id_quanxian, $quanxian_arr)) {
                    $alert_info = '<script type="text/javascript" >alert("非'.$depart_name.'成员不能查看");window.history.back(-1); </script>';
                    $this->show("$alert_info");die;
                }
            }else{
                $this->show('<script type="text/javascript" >alert("普通用户不能查看");window.history.back(-1); </script>');die;
            }
        }
        
        if ($item_info['rackid']==0 || $item_info['rackid'] == 1753) {
            $item_text = "";
            if ($item_info['common_name']) {
                $item_text.="<tr><th width='50%' class='tdright'>名称:</th><td class='tdleft'>".$item_info['common_name']."</td></tr>";
            }
            if ($item_info['itemtypeid'] != 0) {
                $itemtypeid = $item_info['itemtypeid'];
                
                $itemtypeid_info  = M('itemtype')->where('id="'.$itemtypeid.'"  ')->find();
                $item_text.="<tr><th width='50%' class='tdright'>设备类型:</th><td class='tdleft'>".$itemtypeid_info['typedesc']."</td></tr>";
            }
            if ($item_info['manufacturerid'] != 0) {
                $manufacturerid = $item_info['manufacturerid'];
                $agent_info  = M('agents')->where('id="'.$manufacturerid.'"  ')->find();
                $item_text.="<tr><th width='50%' class='tdright'>品牌:</th><td class='tdleft'>".$agent_info['title']."</td></tr>";
            }
            if ($item_info['model']) {
                $item_text.="<tr><th width='50%' class='tdright'>型号:</th><td class='tdleft'>".$item_info['model']."</td></tr>";
            }
            if ($item_info['sn']) {
                $item_text.="<tr><th width='50%' class='tdright'>sn号:</th><td class='tdleft'>".$item_info['sn']."</td></tr>";
            }
            
            $this->assign('id',$id);
            $this->assign('item_text',$item_text);
            $this->assign('url_flag','item_index');
            $this->display();
        }else{
              //非在库或者报废数据
              
            $list = $Items->where('id = "'.$id.'"')->select();
            $label=$list[0]['label'];
            $ip=$list[0]['ipv4'];
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
            
            if ($appointment==1){$status='<font color="#FFD700">预留</font> &nbsp;&nbsp;>> &nbsp;&nbsp;<a href="'.__ROOT__.'/index.php/Home/Racks/rackspace/flag/obligateok/objectid/'.$id.'.html">变更上架</a>';}
            if (empty($locationid) && empty($locareaid) && empty($rackid)){$status='<font color="">下架</font>';}
            if (!empty($locationid) && empty($appointment) && !empty($locareaid) && !empty($rackid)){$status='<font color="#00FFFF">上架</font>';}
            
            
            
            $itemtypeid=$list[0]['itemtypeid'];
            $typelist = M("itemtype")->where('id = "'.$itemtypeid.'"')->select();
            $typedesc=$typelist[0]['typedesc'];
            
            $departmentid=$list[0]['depart_id'];
            $dlist = M("department")->where('id = "'.$departmentid.'"')->select();
            $dname=$dlist[0]['depart_name'];
            
            $racklist = M("ipdb_racks")->where('id = "'.$rackid.'"')->select();
            $usizecount=$racklist[0]['usize'];
            $revnums=$racklist[0]['revnums'];
            $rackcomments=$racklist[0]['name'];
            
            $locationlist = M("ipdb_locations")->where('id = "'.$locationid.'"')->select();
            $location_name=$locationlist[0]['name'];
            $locarealist = M("ipdb_locareas")->where('id = "'.$locareaid.'"')->select();
            $areaname=$locarealist[0]['areaname'];
            
            
            $racktable = $this->showeditrackposdepth($item_info['rackid'],$id);
            $this->assign('flag','on');
            $this->assign('id',$id);
            $this->assign('racktable',$racktable);

            $agents_list = M("agents")->select();
            $agents_list_temp = array();
            if (!empty($agents_list)) {
                foreach ($agents_list as $vo){
                    $id_key = $vo['id'];
                    $agents_list_temp["$id_key"] = $vo['title'];
                }
            }
            
            $this->assign('angnt_list',$agents_list_temp);
            $this->assign('areaname',$areaname);
            $this->assign('location_name',$location_name);
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
            $this->assign('myusize',$usize);
            $this->assign('common_name',$common_name);
            $this->assign('url_flag','item_index');
            $this->display();
         }
            
        
      
        
    } 
    
    
    public function see_junks(){
        
    /**如果为超级管理员**/
        if (IS_ROOT) {
            $itemType = M('itemtype');
                $Agents = M('agents');
                
                $type_list = $itemType->select();
                $depart_list = M('department')->select();
                $agent_list = $Agents->select();
                $location_list = M('ipdb_locations')->select();
                $area_list = M('ipdb_locareas')->select();
                $rack_list = M('ipdb_racks')->select();
                
                
                $location_list = deal_array($location_list, 'id', 'name');
                $area_list = deal_array($area_list, 'id', 'areaname');
                $rack_list = deal_array($rack_list, 'id', 'name');
                $type_list = deal_array($type_list,'id','typedesc');
                $agent_list = deal_array($agent_list,'id','title');
                $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
                
                $items = M('ipdb_items');
                $condition = array();
                $condition['rackid'] = 0;
                if ($_GET['sn']) {
                    $condition['sn'] = array('like',"%".$_GET['sn']."%");
                }
                if ($_GET['common_name']) {
                    $condition['common_name'] = array('like',"%".$_GET['common_name']."%");
                }
                
                $count = $items->where($condition)->count();
                $page = new \Think\Page($count,15);
                $list=$items->order('id desc')->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
                
                $map['sn'] = $_GET['sn'];
                
                $map['common_name'] = $_GET['common_name'];
                foreach($map as $key=>$val) {
                    $p->parameter .= "$key=".urlencode($val)."&";
                }
                $page->setConfig('header','共');
                $page->setConfig('first','«');
                $page->setConfig('last','»');
                //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
                $show = $page->show();
                
                $type_all_list = $itemType->select();
                
                $this->assign('location_list',$location_list);
                $this->assign('area_list',$area_list);
                $this->assign('rack_list',$rack_list);
                $this->assign('state_info',$state_info);
                $this->assign('type_list',$type_list);
                $this->assign('type_all_list',$type_all_list);
                $this->assign('agent_list',$agent_list);
                $this->assign('depart_list',$depart_list);
                $this->assign('depart_arr_now',$depart_arr_now);
                $this->assign('list',$list);
                $this->assign('page',$show);
                $this->assign('url_flag','item_index');
                $this->display();
        }else{
            
            $uid = is_login();
            $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
            
            $itemType = M('itemtype');
            $Agents = M('agents');
            
            $type_list = $itemType->select();
                $depart_list = M('department')->select();
                $agent_list = $Agents->select();
                $location_list = M('ipdb_locations')->select();
                $area_list = M('ipdb_locareas')->select();
                $rack_list = M('ipdb_racks')->select();
                
                
                $location_list = deal_array($location_list, 'id', 'name');
                $area_list = deal_array($area_list, 'id', 'areaname');
                $rack_list = deal_array($rack_list, 'id', 'name');
                $type_list = deal_array($type_list,'id','typedesc');
                $agent_list = deal_array($agent_list,'id','title');
                $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
            
            
            if (empty($arr)) {
                
            }else{
                //$map
                $map_depart['id'] = array('in',$arr);
                $depart_list = M('department')->where($map_depart)->select();
                $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
                
                $items = M('ipdb_items');
                
                $condition = array();
                $condition['depart_id'] = array('in',$arr);
                $condition['rackid'] = 0;
                if ($_GET['sn']) {
                    $condition['sn'] = array('like',"%".$_GET['sn']."%");
                }
                if ($_GET['common_name']) {
                    $condition['common_name'] = array('like',"%".$_GET['common_name']."%");
                }
                
                $count = $items->where($condition)->count();
                $page = new \Think\Page($count,15);
                $list=$items->order('id desc')->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
                
                $map['sn'] = $_GET['sn'];
                $map['common_name'] = $_GET['common_name'];
                foreach($map as $key=>$val) {
                    $p->parameter .= "$key=".urlencode($val)."&";
                }
                $page->setConfig('header','共');
                $page->setConfig('first','«');
                $page->setConfig('last','»');
                //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
                $show = $page->show();
                
                $type_all_list = $itemType->select();
                
                $this->assign('location_list',$location_list);
                $this->assign('area_list',$area_list);
                $this->assign('rack_list',$rack_list);
                $this->assign('type_list',$type_list);
                $this->assign('type_all_list',$type_all_list);
                $this->assign('agent_list',$agent_list);
                $this->assign('depart_list',$depart_list);
                $this->assign('depart_arr_now',$depart_arr_now);
                $this->assign('list',$list);
                $this->assign('page',$show);
            }
            
            
            
            $this->assign('url_flag','item_index');
            $this->display();
            
            
        }
        
    }
    
    
    
    public function see_stores(){
    
        /**如果为超级管理员**/
        if (IS_ROOT) {
            $itemType = M('itemtype');
            $Agents = M('agents');
    
            $type_list = $itemType->select();
            $depart_list = M('department')->select();
            $agent_list = $Agents->select();
            $location_list = M('ipdb_locations')->select();
            $area_list = M('ipdb_locareas')->select();
            $rack_list = M('ipdb_racks')->select();
    
    
            $location_list = deal_array($location_list, 'id', 'name');
            $area_list = deal_array($area_list, 'id', 'areaname');
            $rack_list = deal_array($rack_list, 'id', 'name');
            $type_list = deal_array($type_list,'id','typedesc');
            $agent_list = deal_array($agent_list,'id','title');
            $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
    
            $items = M('ipdb_items');
            $condition = array();
            $condition['rackid'] = 1753;
            if ($_GET['sn']) {
                $condition['sn'] = array('like',"%".$_GET['sn']."%");
            }
            if ($_GET['common_name']) {
                $condition['common_name'] = array('like',"%".$_GET['common_name']."%");
            }
    
            $count = $items->where($condition)->count();
            $page = new \Think\Page($count,15);
            $list=$items->order('id desc')->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
    
            $map['sn'] = $_GET['sn'];
    
            $map['common_name'] = $_GET['common_name'];
            foreach($map as $key=>$val) {
                $p->parameter .= "$key=".urlencode($val)."&";
            }
            $page->setConfig('header','共');
            $page->setConfig('first','«');
            $page->setConfig('last','»');
            //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
            $show = $page->show();
    
            $type_all_list = $itemType->select();
    
            $this->assign('location_list',$location_list);
            $this->assign('area_list',$area_list);
            $this->assign('rack_list',$rack_list);
            $this->assign('state_info',$state_info);
            $this->assign('type_list',$type_list);
            $this->assign('type_all_list',$type_all_list);
            $this->assign('agent_list',$agent_list);
            $this->assign('depart_list',$depart_list);
            $this->assign('depart_arr_now',$depart_arr_now);
            $this->assign('list',$list);
            $this->assign('page',$show);
            $this->assign('url_flag','item_index');
            $this->display();
        }else{
    
            $uid = is_login();
            $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
    
            $itemType = M('itemtype');
            $Agents = M('agents');
    
            $type_list = $itemType->select();
            $depart_list = M('department')->select();
            $agent_list = $Agents->select();
            $location_list = M('ipdb_locations')->select();
            $area_list = M('ipdb_locareas')->select();
            $rack_list = M('ipdb_racks')->select();
    
    
            $location_list = deal_array($location_list, 'id', 'name');
            $area_list = deal_array($area_list, 'id', 'areaname');
            $rack_list = deal_array($rack_list, 'id', 'name');
            $type_list = deal_array($type_list,'id','typedesc');
            $agent_list = deal_array($agent_list,'id','title');
            $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
    
    
            if (empty($arr)) {
    
            }else{
                //$map
                $map_depart['id'] = array('in',$arr);
                $depart_list = M('department')->where($map_depart)->select();
                $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
    
                $items = M('ipdb_items');
    
                $condition = array();
                $condition['depart_id'] = array('in',$arr);
                $condition['rackid'] = 1753;
                if ($_GET['sn']) {
                    $condition['sn'] = array('like',"%".$_GET['sn']."%");
                }
                if ($_GET['common_name']) {
                    $condition['common_name'] = array('like',"%".$_GET['common_name']."%");
                }
    
                $count = $items->where($condition)->count();
                $page = new \Think\Page($count,15);
                $list=$items->order('id desc')->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
    
                $map['sn'] = $_GET['sn'];
                $map['common_name'] = $_GET['common_name'];
                foreach($map as $key=>$val) {
                    $p->parameter .= "$key=".urlencode($val)."&";
                }
                $page->setConfig('header','共');
                $page->setConfig('first','«');
                $page->setConfig('last','»');
                //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
                $show = $page->show();
    
                $type_all_list = $itemType->select();
    
                $this->assign('location_list',$location_list);
                $this->assign('area_list',$area_list);
                $this->assign('rack_list',$rack_list);
                $this->assign('type_list',$type_list);
                $this->assign('type_all_list',$type_all_list);
                $this->assign('agent_list',$agent_list);
                $this->assign('depart_list',$depart_list);
                $this->assign('depart_arr_now',$depart_arr_now);
                $this->assign('list',$list);
                $this->assign('page',$show);
            }
    
    
    
            $this->assign('url_flag','item_index');
            $this->display();
    
    
        }
    
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
    
    
    public function  item_log(){
    
        $uid        =   is_login();
        $nickname = M('user')->getFieldByUid($uid, 'username');
    
        $Ipdb_racks =M("ipdb_racks");
        $Ipdb_rackslog =M("ipdb_rackslog");
        $Ipdb_locareas =M("ipdb_locareas");
        $Ipdb_locations =M("ipdb_locations");
        $Ipdb_items =M("ipdb_items");
    
        $itemid= $_REQUEST['id'];
        
        $racksloglist=$Ipdb_rackslog->where("itemid = $itemid")->order('id desc')->select();
        //var_dump($racksloglist);die;
    
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
    
        $this->assign('url_flag','item_index');
        $this->assign('list',$racksloglist);
        $this->display();
    }
   
    public function view_monitor_bak(){
    
        $list = M('portlist')->select();
    
    
        $text0 = "<ul class='tu-duankou'>";
        $text1 = "<ul class='tu-duankou'>";
        $text2 = "<ul class='tu-duankou'>";
        foreach ($list as $key => $value) {
            if ($key >= 0 && $key<8) {
                if ($value['value'] == 1) {
                    $text0.="<a href='http://211.151.5.12/zichan/index.php/Home/Item/view_monitor_detail.html'><li class='green iconfont icon-configure-port top'><p>".$value['port']."</p><div class='fuceng-div'><p>IP:123.123.123.123</p><p>所属部门：某某某</p><p>合同：HT-12334455</p><p>流量：1343243</p></div></li></a>";
                }else{
                    $text0.="<li class='blue iconfont icon-configure-port top'><p>".$value['port']."</p></li>";
                }
    
            }
    
            if ($key >= 8 && $key<16) {
                if ($value['value'] == 1) {
                    $text1.="<a href='http://211.151.5.12/zichan/index.php/Home/Item/view_monitor_detail.html'><li class='green iconfont icon-configure-port top'><p>".$value['port']."</p></li></a>";
                }else{
                    $text1.="<li class='blue iconfont icon-configure-port top'><p>".$value['port']."</p></li>";
                }
    
            }
    
            if ($key >= 16 && $key<24) {
                if ($value['value'] == 1) {
                    $text2.="<a href='http://211.151.5.12/zichan/index.php/Home/Item/view_monitor_detail.html'><li class='green iconfont icon-configure-port top'><p>".$value['port']."</p></li></a>";
                }else{
                    $text2.="<li class='blue iconfont icon-configure-port top'><p>".$value['port']."</p></li>";
                }
    
            }
    
            if ($key >= 24 && $key<32) {
                if ($value['value'] == 1) {
                    $text0.="<a href='http://211.151.5.12/zichan/index.php/Home/Item/view_monitor_detail.html'><li class='green iconfont icon-configure-port down'><p>".$value['port']."</p></li></a>";
                }else{
                    $text0.="<li class='blue iconfont icon-configure-port down'><p>".$value['port']."</p></li>";
                }
    
            }
    
            if ($key >= 32 && $key<40) {
                if ($value['value'] == 1) {
                    $text1.="<a href='http://211.151.5.12/zichan/index.php/Home/Item/view_monitor_detail.html'><li class='green iconfont icon-configure-port down'><p>".$value['port']."</p></li></a>";
                }else{
                    $text1.="<li class='blue iconfont icon-configure-port down'><p>".$value['port']."</p></li>";
                }
    
            }
    
            if ($key >= 40 && $key<48) {
                if ($value['value'] == 1) {
                    $text2.="<a href='http://211.151.5.12/zichan/index.php/Home/Item/view_monitor_detail.html'><li class='green iconfont icon-configure-port down'><p>".$value['port']."</p></li></a>";
                }else{
                    $text2.="<li class='blue iconfont icon-configure-port down'><p>".$value['port']."</p></li>";
                }
    
            }
    
    
    
        }
        $text0 .= "</ul>";
        $text1 .= "</ul>";
        $text2 .= "</ul>";
    
        $this->assign('one',$text0);
        $this->assign('two',$text1);
        $this->assign('three',$text2);
        $this->display();
    }
    
    
    public function view_monitor(){
        $id = $_REQUEST['id'];// itemid
        $item_info = M('ipdb_items')->where("id = $id")->find();
        $hostid = $item_info['hostid'];
        
        import("Org.Util.ZabbixApiAbstract");
        import("Org.Util.ZabbixApi");
        $api = new \ZabbixApi("http://211.151.5.46/zabbix/api_jsonrpc.php", "Admin", "buzhidao");
        
        $item_in = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifHCInOctets")));
        $in_all = deal_itemid_lastvalue($item_in);
        $item_out = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifHCOutOctets")));
        $out_all = deal_itemid_lastvalue($item_out);
        $item_status = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifOperStatus")));
        $status_all = deal_itemid_lastvalue($item_status);

        
        
        $where_port['hostid'] = $hostid;
        $port_list = M('traffic_portlist')->where($where_port)->select();
        $all_list = array();
        $index = 0;
        
        
        foreach ($port_list as $v){
            $temp = array();
            $index++;
            
            $portid = $v['id'];
            $temp['index'] = $index;
            $temp['portid'] = $portid;
            $temp['portindexid'] = $v['portindexid'];
            $temp['hostid'] = $v['hostid'];
            $ifdesc = $v['ifdescr'];
            $temp['ifdescr'] = deal_if_desc($ifdesc);
            //$temp['ifdescr'] = $v['ifdescr'];
            
            $statusitemid = intval($v['statusitemid']);
            
            
            $initemid = intval($v['initemid']);
            $outitemid = intval($v['outitemid']);
            
            $temp['status'] = $status_all[$statusitemid];
            $temp['in'] = $in_all[$initemid];
            $temp['out'] = $out_all[$outitemid];
            $temp['portname'] = $v['portname'];
            
            $info = M('zz_port')->where("port_id = $portid")->find();
            if ($info) {
                $temp['mis_ip'] = "";
                $temp['code'] = $info['code'];
                $temp['companyname'] = $info['name'];
                $temp['mis_bandwidth'] = $info['bandwidth'];
                $temp['alert'] = deal_traffic_alert($info['bandwidth'],$temp['in'],$temp['out']);
                $temp['contract_id'] = "";
            }else{
                $temp['mis_ip'] = "";
                $temp['code'] = "";
                $temp['companyname'] = "";
                $temp['mis_bandwidth'] = "";
                $temp['contract_id'] = "";
                $temp['alert'] = "";
            }
            $all_list[]=$temp;
        }

        
        $item_get = $api->itemGet($params=array("output"=>"extend","hostids"=>"$hostid","search"=>array("key_"=>"sysDescr")));
        $device_info = $item_get[0]->lastvalue;
        
        $item_get = $api->itemGet($params=array("output"=>"extend","hostids"=>"$hostid","search"=>array("key_"=>"sysUpTime")));
        $runtime_info = $item_get[0]->lastvalue;
        
        $item_get = $api->itemGet($params=array("output"=>"extend","hostids"=>"$hostid","search"=>array("key_"=>"sysName")));
        $sys_name = $item_get[0]->lastvalue;
        
       
        $this->assign('sys_name',$sys_name);
        $this->assign('runtime_info',round($runtime_info/3600,4));
        $this->assign('device_info',$device_info);
        $this->assign('list',$all_list);
        $this->assign('url_flag','item_index');
        $this->display();
    }
    
    public function view_monitor_detail(){
        
        header("Content-type: text/html; charset=utf-8");
        $id = $_REQUEST['id'];
        $info = array();
        $where_port['id'] = $id;
        $port_info = M('traffic_portlist')->where("id = $id")->find();
        $info['portname'] = $port_info['portname'];
        $info['hostid'] = $port_info['hostid'];
        $gid = $port_info['gid'];
        $ugurl = "http://211.151.5.53/graph.php?id=$gid";
        $rez = outputCurl($ugurl);
        $hostid = $port_info['hostid'];
        $item_info = M('ipdb_items')->where("hostid = $hostid")->find();
        $info['wl_device_name'] = $item_info['common_name'];
        
        $zi_info = M('zz_port')->where("port_id = $id")->find();
        if ($zi_info) {
            $info['companyid'] = $zi_info['companyid'];
            $info['code'] = $zi_info['code'];
            $info['companyname'] = $zi_info['name'];
            $info['mis_bandwidth'] = $zi_info['bandwidth'];

        }else{
            $info['code'] = "";
            $info['companyid'] = "";
            $info['companyname'] = "";
            $info['mis_bandwidth'] = "";
        }

        $this->assign('gid',$gid);
        $this->assign('info',$info);
        $this->assign('url_flag','item_index');
        $this->display();
    }
    
    public function export_data(){
        $depart_list = M('department')->select();
        $this->assign('depart_list',$depart_list);
        $this->assign('url_flag','item_index');
        $this->display();
    }
    
    public function monitor_del(){
        
        $itemid = $_REQUEST['itemid'];
        $item_info = M('ipdb_items')->where("id = $itemid")->find();
        $hostid= $item_info['hostid'];
        if (!empty($hostid)) {
            $da['hostid'] = null;
            M('ipdb_items')->where("id = $itemid")->save($da);
            
            import("Org.Util.ZabbixApiAbstract");
            import("Org.Util.ZabbixApi");
            $api = new \ZabbixApi("http://211.151.5.46/zabbix/api_jsonrpc.php", "Admin", "buzhidao");
            $del_host=$api->hostDelete($params=array($hostid));
            $this->show('<script type="text/javascript" >alert("取消监控成功"); window.history.go(-1);</script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert(""取消监控失败");window.history.go(-1);</script>');die;
        }
    }
    
    
    
    
        
    
}
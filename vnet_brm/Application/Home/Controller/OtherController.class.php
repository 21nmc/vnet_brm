<?php
namespace Home\Controller;
use Think\Controller;
class  OtherController extends HomeController {
    //出口带宽分布
    public function citylist(){
        $Demo = M('city');
        $count = $Demo->count();
        $page = new \Think\Page($count,20);
         $name= I('post.name');

        if ( IS_POST ) {
        echo $name;
        }else{
        $list=$Demo->order('id desc')->limit($page->firstRow,$page->listRows)->select();
       
        
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        
        $user_group_list =  M('usergroup')->select();
        $user_group_list_info =  deal_array($user_group_list,'id','groupname');
        }
        
        $this->assign('page',$page->show());
        $this->assign('list',$list);
        $this->assign('usergrouplist',$user_group_list_info); //用户组列表
        $this->assign('url_flag','weekchukou'); //left flag
        $this->display();
    }


    public function add_param() {
    
        if(!is_login()){
            $this->redirect("User/login");
        }
        $uid        =   is_login();
    
        $zichan = M('traffic_newzichan');
        $group = M('department');
        
        if(IS_POST){
             
           
            $group_list=I('post.group_list');
            $name=I('post.name');
            $ip=I('post.ip');
            $snmp=I('post.snmp');
    
       $check=$zichan->where('host = "'.$ip.'"')->order('id DESC')->limit(1)->select();
   
                          
         if (empty($check)){
            $map['host']=$ip;
            $map['name']=$name;
            $map['snmp']=$snmp;
            $map['section']=$group_list;
           //$list=$zichan->data($map)->add();

            import("Org.Util.ZabbixApiAbstract");
            import("Org.Util.ZabbixApi");
            $api = new \ZabbixApi("http://211.151.5.27/zabbix/api_jsonrpc.php", "api", "2jlijjri");
           $a=array("10121","10440","10120");
           $random_keys=array_rand($a,1);
           $proxyid=$a[$random_keys];
          $host_add= $api->hostCreate($params=array("host"=>"$name-zhoubao","name"=>"$name-zhoubao","interfaces"=>array(array("type"=>2, "main"=>1, "useip"=>1, "ip"=>$ip, "dns"=>"", "port"=>"161")),"proxy_hostid"=>"$proxyid","groups"=>array(array("groupid"=>19)),"macros"=>array(array("macro"=>'{$SNMP_COMMUNITY}',"value"=>$snmp))));
           $hostcc =$host_add->hostids;
          $hostid = $hostcc[0];
              $map['hostid'] = $hostcc[0];
              $a = system("php /var/www/html/Traffi/walk2.php $snmp $ip $hostid");
              $list=$zichan->data($map)->add();
               if (!empty($list)){
                $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Weekreport/index")}"; </script>');
                exit();
            }else{
                $this->show('<script type="text/javascript" >alert("添加失败");window.location.href="{:U("Weekreport/index")}"; </script>');
                exit();
            }
           }
    
        }
    
        $group_list = $group->select();
        $this->assign('group_list',$group_list); //用户组列表
        $this->assign('url_flag','owner_user_list'); //left flag
        $this->display();
    }



public function plist(){
      
    if(!is_login()){
        $this->redirect("User/login");
    }
    
    $Demo = M('traffic_portlist2'); 
    $id   = $_REQUEST['id'];
    if ( IS_POST ) {
        $name= I('post.name');
        $count = $Demo->where('host like "%'.$name.'%" or portname like "%'.$name.'%" and hostid="'.$id.'" ')->field("id")->count();
        $page = new \Think\Page($count,20);       
        $list2=$Demo->where('host like "%'.$name.'%" or portname like "%'.$name.'%" and hostid="'.$id.'" ')->field('id,portindexid,portname,hostid,host,status')->order('id ')->limit($page->firstRow,$page->listRows)->select();
             
    }else{
        $count = $Demo->field("id")->where('hostid="'.$id.'"')->count();
        $page = new \Think\Page($count,20);
        $list=$Demo->field('id,portindexid,portname,hostid,host,status')->where('hostid="'.$id.'"')->order('id desc')->limit($page->firstRow,$page->listRows)->select();
    }
    $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    $this->assign('page',$page->show());
    $this->assign('list',$list);
    $this->assign('url_flag','weekchukou'); //left flag
    $this->display();
}

    public function add_port() {
    
        if(!is_login()){
            $this->redirect("User/login");
        }
        $uid        =   is_login();
    
        $zichan = M('traffic_newzichan');
        $group = M('resource_zabbix_type');
        
          $id   = $_REQUEST['id'];
        $Demo = M('traffic_portlist2');
       $list = $Demo->where('id="'.$id.'"')->field('portindexid,portname,hostid')->select();
        $group_list = $group->select();
        $this->assign('group_list',$group_list); //用户组列表
        $this->assign('list',$list);
        $this->assign('url_flag','weekchukou'); //left flag
        $this->display();
}



     public function add_portindex() {
    
        if(!is_login()){
            $this->redirect("User/login");
        }
        $uid        =   is_login();
    
        $zichan = M('resource_zabbix2');
        $group = M('resource_zabbix_type');
        $hostdb = M('traffic_newzichan');
        $Demo2 = M('traffic_portlist2');

        $hostid = $_POST['hostid']; 
        $portname = $_POST['portname'];
        $portindexid = $_POST['portindexid'];
        $type_id = $_POST['group_list'];

          $typename = $group->where('id="'.$type_id.'"')->field('type_name')->select();
          $type_name = $typename[0][type_name];
         $h = $hostdb->where('hostid="'.$hostid.'"')->select();
         $name = $h[0][name];
         $section = $h[0][section];
         $device_name = $name."-".$portname;

     

         


         import("Org.Util.ZabbixApiAbstract");
   import("Org.Util.ZabbixApi");
   $api = new \ZabbixApi("http://211.151.5.27/zabbix/api_jsonrpc.php", "api", "2jlijjri");

  
    $inname = $device_name."-".Inbound;
    $outname = $device_name."-".Outbound;
    $inalias = Intbound."-".$alias;
    $outalias = Outtbound."-".$alias;
     
    $inkey_ = ifHCInOctets.".".$portindexid;
    $outkey_ = ifHCOutOctets.".".$portindexid;

// echo $inname." ".$inalias." ".$inkey_."<br\>";
// echo $outname." ".$$outalias." ".$outkey_."<br\>";

$interfaceid_get=$api->hostinterfaceGet($params=array("hostids"=>$hostid,"sortfield"=>interfaceid));
$interfaceid = $interfaceid_get[0]->interfaceid;

$add_Inbound_item=$api->itemCreate($params=array("name"=>$inname,"key_"=>$inkey_,"hostid"=>$hostid,"type"=>4,"status"=>0,"value_type"=>3,"interfaceid"=>$interfaceid, "delay"=>300, "history"=>180,"delta"=>1,"valuemapid"=>0,"units"=>bps,"snmp_oid"=>$inkey_,"trends"=>365,"snmp_community"=>'{$SNMP_COMMUNITY}',"formula"=>8,"multiplier"=>1));

   $add_Outbound_item=$api->itemCreate($params=array("name"=>$outname,"key_"=>$outkey_,"hostid"=>$hostid,"type"=>4,"status"=>0,"value_type"=>3,"interfaceid"=>$interfaceid, "delay"=>300, "history"=>180,"delta"=>1,"valuemapid"=>0,"units"=>bps,"snmp_oid"=>$outkey_,"trends"=>365,"snmp_community"=>'{$SNMP_COMMUNITY}',"formula"=>8,"multiplier"=>1));



         $Inbound_itemid=$add_Inbound_item->itemids[0];
         $Outbound_itemid=$add_Outbound_item->itemids[0];


         $add_graph=$api->graphCreate($params=array("name"=>$device_name,"width"=>900,"height"=>200,"ymin_type"=>0,"ymax_type"=>0,"yaxismin"=>'0.00',"yaxismax"=>'100.00',"ymin_itemid"=>0,"ymax_itemid"=>0,"show_work_period"=>1,"show_triggers"=>1,"graphtype"=>0,"show_legend"=>1,"show_3d"=>0,"percent_left"=>0,"percent_right"=>0,"gitems"=>array("0"=>array("gitemid"=>0,"graphid"=>0,"itemid"=>$Inbound_itemid,"sortorder"=>0,"flags"=>0,"type"=>0,"calc_fnc"=>2,"drawtype"=>1,"yaxisside"=>0,"color"=>'00EE00'),"1"=>array("gitemid"=>0,"graphid"=>0,"itemid"=>$Outbound_itemid,"sortorder"=>1,"flags"=>0,"type"=>0,"calc_fnc"=>2,"drawtype"=>0,"yaxisside"=>0,"color"=>'0000EE'))));
    $graph_id=$add_graph->graphids[0];
 
         $map['hostid']=$hostid;
         $map['type_id']=$type_id;
         $map['device_name']=$device_name;
         $map['type_name']=$type_name;
         $map['depart_id']=$section;
         $map['initemid']=$Inbound_itemid;
         $map['outitemid']=$Outbound_itemid;

         $map2['portindexid'] = $portindexid;
         $map2['hostid'] = $hostid;


       // print_r($_POST);
           $list=$zichan->data($map)->add();
          $list2=$Demo2->where($map2)->setField(array('status'=>1));
         if (!empty($list)){
                $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Weekreport/index")}"; </script>');
                exit();
            }else{
                $this->show('<script type="text/javascript" >alert("添加失败");window.location.href="{:U("Weekreport/index")}"; </script>');
                exit();
            }

        $this->assign('url_flag','weekchukou'); //left flag
        $this->display();
    }


    public function port_list(){
    
        $types = M('resource_zabbix_type');
        $ports = M('resource_zabbix');
        
        
        $type_id = $_REQUEST['type_id'];
        $type_info = $types->where("id = $type_id")->find();
        $port_list = $ports->where("type_id = $type_id")->select();
        
        $depart_id = $type_info['depart_id'];
        $type_list = $types->where("depart_id = $depart_id")->select();
        
        $this->assign("type_list",$type_list);
        $this->assign("depart_id",$depart_id);
        $this->assign("port_list",$port_list);
        $this->assign('url_flag','chukou'); //left flag
        $this->assign("type_id",$type_id);
        $this->display();
    }
    

}

<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller {
   
   
    public function auto_del(){
        //倒数据
        $alert = M('cust_alert'); // 实例化一个model对象 没有对应任何数据表
        $max_find = $alert->field("max(version) as version")->select();
        $version = intval($max_find[0]['version']);
        $mini_version = $version - 72;
        $alert->where("version < $mini_version")->delete();
   }
    
   public function index(){
       //倒数据
       $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
       $all = $Model->query("select DISTINCT host as host,hostid from vnet_traffic_portlist GROUP BY hostid");
       
       $locations = M('locations','no','mysql://root:mysql@203.196.0.170/phpipam');
       
       $llist = $locations->select();
       var_dump($llist);die();
       
   }
    
   public function test(){
       $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
       $all = $Model->query("select DISTINCT host as host,hostid from vnet_traffic_portlist GROUP BY hostid");
       $items = M('ipdb_items');
       foreach ($all as $one){
           $hostid = $one['hostid'];
           $ipv4 = $one['host'];
           $kk = $items->where("hostid = $hostid")->find();
           if ($kk['ipv4'] != $ipv4) {
               var_dump($ipv4);
           }
       }
       
       
   }
    
    
    public function deal_alerts(){
        
        import("Org.Util.ZabbixApiAbstract");
        import("Org.Util.ZabbixApi");
        $api = new \ZabbixApi("http://211.151.5.46/zabbix/api_jsonrpc.php", "Admin", "buzhidao");
       
        $alert = M('cust_alert');
        $max_find = $alert->field("max(version) as version")->select();
        $version = intval($max_find[0]['version']);
        $version = $version + 1;
        
        $items = M('ipdb_items')->where("hostid is not null and itemtypeid = 1 and depart_id = 5")->select();
        foreach ($items as $one){
            $hostid = $one['hostid'];
            $common_name = $one['common_name'];
          
            $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
            $all = $Model->query("SELECT b.portname as portname,b.initemid as initemid,b.statusitemid as statusitemid, b.outitemid as outitemid,b.ifDescr as ifDescr,a.name as name,a.fazhi as fazhi FROM vnet_traffic_portlist as b left join vnet_zz_port as a on b.id = a.port_id where b.hostid = $hostid");
            
            
            if (!empty($all)) {
                $item_in = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifHCInOctets")));
                $in_all = deal_itemid_lastvalue($item_in);
                $item_out = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifHCOutOctets")));
                $out_all = deal_itemid_lastvalue($item_out);
                $item_status = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifOperStatus")));
                $status_all = deal_itemid_lastvalue($item_status);
                
                
                //$temp = array();
                foreach ($all as $o){
                    $ifdesc = $o['ifdescr'];
                    $fazhi_now = $o['fazhi'];
                    
                    
                    if ($fazhi_now == "无") {
                        continue;
                    }
                    
                    $initemid = $o['initemid'];
                    $outitemid = $o['outitemid'];
                    $stateid = $o['statusitemid'];
                    $inflow = intval($in_all[$initemid]);
                    $outflow = intval($out_all[$outitemid]);
                    $state = $status_all[$stateid];
                    $name = $o['name'];
                    
                    if ($name != null || $name != "") {
                                if (strpos($ifdesc, "Interface") || $ifdesc=="" || $ifdesc == null) {
                                    if ($inflow > 1024*1024 || $outflow>1024*1024 ) {
                                       
                                        $data['item_name'] = $common_name;
                                        $data['port_desc'] = $o['ifdescr'];
                                        $data['portname'] = $o['portname'];
                                        $data['cust'] = $o['name'];
                                        $data['in'] = $inflow."";
                                        $data['out'] = $outflow."";
                                        $data['alert_info'] = "端口无描述但是 有流量";
                                        $data['type'] = 0;
                                        $data['state'] = $state;
                                        $data['version'] = $version;
                                        $data['depart_id'] = 5;
                                        $data['beyond'] = deal_which_bigger($inflow, $outflow);
                                        $alert->add($data);
                                    }
                                }
                                
                                $fazhi = intval($fazhi_now) * 1024 * 1024;
                                
                                if ($inflow > $fazhi || $outflow > $fazhi) {
                                  
                                    $data['item_name'] = $common_name;
                                    $data['port_desc'] = $o['ifdescr'];
                                    $data['portname'] = $o['portname'];
                                    $data['cust'] = $o['name'];
                                    $data['in'] = $inflow."";
                                    $data['out'] = $outflow."";

                                    $data['fazhis'] = $fazhi_now."";
                                    $data['alert_info'] = "端口流量超阀值";
                                    $data['type'] = 1;
                                    $data['state'] = $state;
                                    $data['version'] = $version;
                                    $data['depart_id'] = 5;
                                    
                                    if ($inflow > $fazhi) {
                                        $data['beyond'] = $inflow-$fazhi;
                                    }else{
                                        $data['beyond'] = $outflow-$fazhi;
                                    }
                                    
                                    $alert->add($data);
                                }
                    }else{
                        if (strpos($ifdesc, "Interface") || $ifdesc=="" || $ifdesc == null) {
                            if ($inflow > 1024*1024 || $outflow>1024*1024 ) {
                                $data['item_name'] = $common_name;
                                $data['port_desc'] = $o['ifdescr'];
                                $data['portname'] = $o['portname'];
                                $data['cust'] = $o['name'];
                                $data['in'] = $inflow."";
                                $data['out'] = $outflow."";
                                $data['alert_info'] = "端口无描述但是 有流量";
                                $data['type'] = 0;
                                $data['state'] = $state;
                                $data['version'] = $version;
                                $data['depart_id'] = 5;
                                $data['beyond'] = deal_which_bigger($inflow, $outflow);
                                $alert->add($data);
                            }
                        }
                    }
                    
                   
                    
                    
                }
             
            }else{
                continue;
            }
             
        }
        /**华东**/
        
        $items_huadong = M('ipdb_items')->where("hostid is not null and itemtypeid = 1 and depart_id = 4")->select();
        foreach ($items_huadong as $one){
            $hostid = $one['hostid'];
            $common_name = $one['common_name'];
        
            $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
            $all = $Model->query("SELECT b.portname as portname,b.initemid as initemid,b.statusitemid as statusitemid, b.outitemid as outitemid,b.ifDescr as ifDescr,a.name as name,a.fazhi as fazhi FROM vnet_traffic_portlist as b left join vnet_zz_port as a on b.id = a.port_id where b.hostid = $hostid");
        
        
            if (!empty($all)) {
                $item_in = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifHCInOctets")));
                $in_all = deal_itemid_lastvalue($item_in);
                $item_out = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifHCOutOctets")));
                $out_all = deal_itemid_lastvalue($item_out);
                $item_status = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifOperStatus")));
                $status_all = deal_itemid_lastvalue($item_status);
        
        
                //$temp = array();
                foreach ($all as $o){
                    $ifdesc = $o['ifdescr'];
                    $fazhi_now = $o['fazhi'];
        
        
                    if ($fazhi_now == "无") {
                        continue;
                    }
        
                    $initemid = $o['initemid'];
                    $outitemid = $o['outitemid'];
                    $stateid = $o['statusitemid'];
                    $inflow = intval($in_all[$initemid]);
                    $outflow = intval($out_all[$outitemid]);
                    $state = $status_all[$stateid];
                    $name = $o['name'];
        
                    if ($name != null || $name != "") {
                        if (strpos($ifdesc, "Interface") || $ifdesc=="" || $ifdesc == null) {
                            if ($inflow > 1024*1024 || $outflow>1024*1024 ) {
                                 
                                $data['item_name'] = $common_name;
                                $data['port_desc'] = $o['ifdescr'];
                                $data['portname'] = $o['portname'];
                                $data['cust'] = $o['name'];
                                $data['in'] = $inflow."";
                                $data['out'] = $outflow."";
                                $data['alert_info'] = "端口无描述但是 有流量";
                                $data['type'] = 0;
                                $data['state'] = $state;
                                $data['version'] = $version;
                                $data['depart_id'] = 4;
                                $data['beyond'] = deal_which_bigger($inflow, $outflow);
                                $alert->add($data);
                            }
                        }
        
                        $fazhi = intval($fazhi_now) * 1024 * 1024;
        
                        if ($inflow > $fazhi || $outflow > $fazhi) {
        
                            $data['item_name'] = $common_name;
                            $data['port_desc'] = $o['ifdescr'];
                            $data['portname'] = $o['portname'];
                            $data['cust'] = $o['name'];
                            $data['in'] = $inflow."";
                            $data['out'] = $outflow."";
        
                            $data['fazhis'] = $fazhi_now."";
                            $data['alert_info'] = "端口流量超阀值";
                            $data['type'] = 1;
                            $data['state'] = $state;
                            $data['version'] = $version;
                            $data['depart_id'] = 4;
                            
                            if ($inflow > $fazhi) {
                                $data['beyond'] = $inflow-$fazhi;
                            }else{
                                $data['beyond'] = $outflow-$fazhi;
                            }
                            
                            $alert->add($data);
                        }
                    }else{
                        if (strpos($ifdesc, "Interface") || $ifdesc=="" || $ifdesc == null) {
                            if ($inflow > 1024*1024 || $outflow>1024*1024 ) {
                                $data['item_name'] = $common_name;
                                $data['port_desc'] = $o['ifdescr'];
                                $data['portname'] = $o['portname'];
                                $data['cust'] = $o['name'];
                                $data['in'] = $inflow."";
                                $data['out'] = $outflow."";
                                $data['alert_info'] = "端口无描述但是 有流量";
                                $data['type'] = 0;
                                $data['state'] = $state;
                                $data['version'] = $version;
                                $data['depart_id'] = 4;
                                $data['beyond'] = deal_which_bigger($inflow, $outflow);
                                $alert->add($data);
                            }
                        }
                    }
        
                     
        
        
                }
                 
            }else{
                continue;
            }
             
        }
        
        
        $items_huanan = M('ipdb_items')->where("hostid is not null and itemtypeid = 1 and depart_id = 6")->select();
        foreach ($items_huanan as $one){
            $hostid = $one['hostid'];
            $common_name = $one['common_name'];
        
            $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
            $all = $Model->query("SELECT b.portname as portname,b.initemid as initemid,b.statusitemid as statusitemid, b.outitemid as outitemid,b.ifDescr as ifDescr,a.name as name,a.fazhi as fazhi FROM vnet_traffic_portlist as b left join vnet_zz_port as a on b.id = a.port_id where b.hostid = $hostid");
        
        
            if (!empty($all)) {
                $item_in = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifHCInOctets")));
                $in_all = deal_itemid_lastvalue($item_in);
                $item_out = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifHCOutOctets")));
                $out_all = deal_itemid_lastvalue($item_out);
                $item_status = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifOperStatus")));
                $status_all = deal_itemid_lastvalue($item_status);
        
        
                //$temp = array();
                foreach ($all as $o){
                    $ifdesc = $o['ifdescr'];
                    $fazhi_now = $o['fazhi'];
        
        
                    if ($fazhi_now == "无") {
                        continue;
                    }
        
                    $initemid = $o['initemid'];
                    $outitemid = $o['outitemid'];
                    $stateid = $o['statusitemid'];
                    $inflow = intval($in_all[$initemid]);
                    $outflow = intval($out_all[$outitemid]);
                    $state = $status_all[$stateid];
                    $name = $o['name'];
        
                    if ($name != null || $name != "") {
                        if (strpos($ifdesc, "Interface") || $ifdesc=="" || $ifdesc == null) {
                            if ($inflow > 1024*1024 || $outflow>1024*1024 ) {
                                 
                                $data['item_name'] = $common_name;
                                $data['port_desc'] = $o['ifdescr'];
                                $data['portname'] = $o['portname'];
                                $data['cust'] = $o['name'];
                                $data['in'] = $inflow."";
                                $data['out'] = $outflow."";
                                $data['alert_info'] = "端口无描述但是 有流量";
                                $data['type'] = 0;
                                $data['state'] = $state;
                                $data['version'] = $version;
                                $data['depart_id'] = 6;
                                $data['beyond'] = deal_which_bigger($inflow, $outflow);
                                $alert->add($data);
                            }
                        }
        
                        $fazhi = intval($fazhi_now) * 1024 * 1024;
        
                        if ($inflow > $fazhi || $outflow > $fazhi) {
        
                            $data['item_name'] = $common_name;
                            $data['port_desc'] = $o['ifdescr'];
                            $data['portname'] = $o['portname'];
                            $data['cust'] = $o['name'];
                            $data['in'] = $inflow."";
                            $data['out'] = $outflow."";
        
                            $data['fazhis'] = $fazhi_now."";
                            $data['alert_info'] = "端口流量超阀值";
                            $data['type'] = 1;
                            $data['state'] = $state;
                            $data['version'] = $version;
                            $data['depart_id'] = 6;
                            
                            if ($inflow > $fazhi) {
                                $data['beyond'] = $inflow-$fazhi;
                            }else{
                                $data['beyond'] = $outflow-$fazhi;
                            }
                            
                            $alert->add($data);
                        }
                    }else{
                        if (strpos($ifdesc, "Interface") || $ifdesc=="" || $ifdesc == null) {
                            if ($inflow > 1024*1024 || $outflow>1024*1024 ) {
                                $data['item_name'] = $common_name;
                                $data['port_desc'] = $o['ifdescr'];
                                $data['portname'] = $o['portname'];
                                $data['cust'] = $o['name'];
                                $data['in'] = $inflow."";
                                $data['out'] = $outflow."";
                                $data['alert_info'] = "端口无描述但是 有流量";
                                $data['type'] = 0;
                                $data['state'] = $state;
                                $data['version'] = $version;
                                $data['depart_id'] = 6;
                                $data['beyond'] = deal_which_bigger($inflow, $outflow);
                                $alert->add($data);
                            }
                        }
                    }
        
                     
        
        
                }
                 
            }else{
                continue;
            }
             
        }
        
        
        $items_chuanshu = M('ipdb_items')->where("hostid is not null and itemtypeid = 1 and depart_id = 8")->select();
        foreach ($items_chuanshu as $one){
            $hostid = $one['hostid'];
            $common_name = $one['common_name'];
        
            $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
            $all = $Model->query("SELECT b.portname as portname,b.initemid as initemid,b.statusitemid as statusitemid, b.outitemid as outitemid,b.ifDescr as ifDescr,a.name as name,a.fazhi as fazhi FROM vnet_traffic_portlist as b left join vnet_zz_port as a on b.id = a.port_id where b.hostid = $hostid");
        
        
            if (!empty($all)) {
                $item_in = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifHCInOctets")));
                $in_all = deal_itemid_lastvalue($item_in);
                $item_out = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifHCOutOctets")));
                $out_all = deal_itemid_lastvalue($item_out);
                $item_status = $api->itemGet($params=array("output"=>"extend","hostids"=>$hostid,"selectHosts"=>"extend","search"=>array("snmp_oid"=>"ifOperStatus")));
                $status_all = deal_itemid_lastvalue($item_status);
        
        
                //$temp = array();
                foreach ($all as $o){
                    $ifdesc = $o['ifdescr'];
                    $fazhi_now = $o['fazhi'];
        
        
                    if ($fazhi_now == "无") {
                        continue;
                    }
        
                    $initemid = $o['initemid'];
                    $outitemid = $o['outitemid'];
                    $stateid = $o['statusitemid'];
                    $inflow = intval($in_all[$initemid]);
                    $outflow = intval($out_all[$outitemid]);
                    $state = $status_all[$stateid];
                    $name = $o['name'];
        
                    if ($name != null || $name != "") {
                        if (strpos($ifdesc, "Interface") || $ifdesc=="" || $ifdesc == null) {
                            if ($inflow > 1024*1024 || $outflow>1024*1024 ) {
                                 
                                $data['item_name'] = $common_name;
                                $data['port_desc'] = $o['ifdescr'];
                                $data['portname'] = $o['portname'];
                                $data['cust'] = $o['name'];
                                $data['in'] = $inflow."";
                                $data['out'] = $outflow."";
                                $data['alert_info'] = "端口无描述但是 有流量";
                                $data['type'] = 0;
                                $data['state'] = $state;
                                $data['version'] = $version;
                                $data['depart_id'] = 8;
                                $data['beyond'] = deal_which_bigger($inflow, $outflow);
                                $alert->add($data);
                            }
                        }
        
                        $fazhi = intval($fazhi_now) * 1024 * 1024;
        
                        if ($inflow > $fazhi || $outflow > $fazhi) {
        
                            $data['item_name'] = $common_name;
                            $data['port_desc'] = $o['ifdescr'];
                            $data['portname'] = $o['portname'];
                            $data['cust'] = $o['name'];
                            $data['in'] = $inflow."";
                            $data['out'] = $outflow."";
        
                            $data['fazhis'] = $fazhi_now."";
                            $data['alert_info'] = "端口流量超阀值";
                            $data['type'] = 1;
                            $data['state'] = $state;
                            $data['version'] = $version;
                            $data['depart_id'] = 8;
        
                            if ($inflow > $fazhi) {
                                $data['beyond'] = $inflow-$fazhi;
                            }else{
                                $data['beyond'] = $outflow-$fazhi;
                            }
        
                            $alert->add($data);
                        }
                    }else{
                        if (strpos($ifdesc, "Interface") || $ifdesc=="" || $ifdesc == null) {
                            if ($inflow > 1024*1024 || $outflow>1024*1024 ) {
                                $data['item_name'] = $common_name;
                                $data['port_desc'] = $o['ifdescr'];
                                $data['portname'] = $o['portname'];
                                $data['cust'] = $o['name'];
                                $data['in'] = $inflow."";
                                $data['out'] = $outflow."";
                                $data['alert_info'] = "端口无描述但是 有流量";
                                $data['type'] = 0;
                                $data['state'] = $state;
                                $data['version'] = $version;
                                $data['depart_id'] = 8;
                                $data['beyond'] = deal_which_bigger($inflow, $outflow);
                                $alert->add($data);
                            }
                        }
                    }
        
                     
        
        
                }
                 
            }else{
                continue;
            }
             
        }
        
        
        
        
    }
    
    
    
    
    
    
    
}
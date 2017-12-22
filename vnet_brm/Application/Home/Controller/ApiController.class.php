<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;
use Home\Model\AuthRuleModel;
/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class ApiController extends Controller {
    
    
    public function up_cust_new(){
        $cust_list = M('zzzz_temp_cust')->select();
        foreach ($cust_list as $val) {
            $cust_code = $val['cust_code'];
            $id= $val['id'];
            $first = substr($cust_code, 0,1);
            if(is_numeric($first)){
                $cust_arr['cust_code'] = '00'.$cust_code;
                M('zzzz_temp_cust')->where("id = $id")->save($cust_arr);
            }
        }
    }
    
    public function in_port_new(){
        $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表;
        $p_list = $Model->query("SELECT * from vnet_zz_port WHERE code in (SELECT DISTINCT cust_code as cust_code from vnet_zzzz_temp_cust)");
        foreach ($p_list as $val) {
            $arr['cust_code'] = $val['code'];
            $arr['cust_name'] = $val['name'];
            $arr['device_name'] = $val['device_name'];
            $arr['wl_device_name'] = $val['wl_device_name'];
            $arr['port_number'] = $val['port_number'];
            $arr['port_desc'] = $val['port_desc'];
            $arr['bandwidth'] = $val['bandwidth'];
            $arr['port_id'] = $val['port_id'];
            $arr['fazhi'] = $val['fazhi'];
            $arr['pact_code'] = $val['pact_code'];
            M('zzzz_temp_port')->add($arr);
        }
    }
    
    
    public function up_zzzz_port(){
        $cust_list = M('zzzz_temp_port')->select();
        foreach ($cust_list as $val) {
            $port_number = $val['port_number'];
            $id= $val['id'];
            $pact = M('erp_pact_ports')->where("port_code = '$port_number'")->find();
            if($pact){
                if ($pact['ip'] != "") {
                    $cust_arr['ips'] = $pact['ip'];
                    M('zzzz_temp_port')->where("id = $id")->save($cust_arr);
                }
                
            }
        }
    }
    
    public function up_zzzz_port_se(){
        $cust_list = M('zzzz_temp_port')->select();
        foreach ($cust_list as $val) {
            $port_number = $val['port_number'];
            $id= $val['id'];
            $pact = M('zz_zz_zichan')->where("port_code = '$port_number'")->find();
            if($pact){
                if ($pact['ip'] != "") {
                    $cust_arr['ips'] = $pact['ips'];
                    M('zzzz_temp_port')->where("id = $id")->save($cust_arr);
                }
            }
        }
    }
    
    public function up_zzzz_port_zone(){
        $cust_list = M('zzzz_temp_port')->select();
        foreach ($cust_list as $val) {
            $port_number = $val['port_number'];
            $id= $val['id'];

                $pact = M('erp_pact_port')->where("port_code = '$port_number'")->find();
                if($pact){
      
                        $cust_arr['belong_zone'] = $pact['belong_zone'];
                        M('zzzz_temp_port')->where("id = $id")->save($cust_arr);
                    
                }
         }
            
        
    }
    
    
    public function up_zzzz_port_desc(){
        $cust_list = M('zzzz_temp_port')->select();
        foreach ($cust_list as $val) {
            $port_id = $val['port_id'];
            $id= $val['id'];
            if (!empty($port_id)) {
                $pact = M('traffic_portlist')->where("id = $port_id")->find();
                if($pact){
                    if ($pact['ifdescr'] != "") {
                        $cust_arr['port_desc_monitor'] = $pact['ifdescr'];
                        M('zzzz_temp_port')->where("id = $id")->save($cust_arr);
                    }
                }
            }
    
        }
    }
    
    

    
    
    
    
    
    public function doing(){
        $this->display();
    }
    
	public function index(){
		$depart = M('department')->select();
		$temp = array();
		foreach ($depart as $value) {
		    $temp[]=$value['depart_name'];
		}
		echo implode('*', $temp);die;
	}
	
	public function search(){
	    $sn = $_REQUEST['sn'];
	    
	    $check_info =M('ipdb_items')->where('sn = "'.$sn.'" ')->find();
	    $item_t = M('itemtype')->select();
	    $item_arr = deal_array($item_t,'id','typedesc');
	    
	    $locations = M('ipdb_locations')->select();
	    $locations_arr = deal_array($locations,'id','name');
	    
	    $departs = M('department')->select();
	    $departs_arr = deal_array($departs,'id','depart_name');
	    
	    $areas = M('ipdb_locareas')->select();
	    $areas_arr = deal_array($areas,'id','areaname');
	    
	    $racks = M('ipdb_racks')->select();
	    $racks_arr = deal_array($racks,'id','name');
	    
	    $str= "设备信息: \n";
	    $str.= "SN号: ".$check_info['sn']."\n";
	    $str.="设备名: ".$check_info['common_name']."\n";
	    $str.="型号名: ".$check_info['model']."\n";
	    $str.="U位数: ".$check_info['usize']."U\n";
	    $str.="设备类型: ".$item_arr[$check_info['itemtypeid']]."\n";
	    $str.="所属部门: ".$departs_arr[$check_info['depart_id']]."\n";
	    $str.="机房名: ".$locations_arr[$check_info['locationid']]."\n";
	    $str.="房间名: ".$areas_arr[$check_info['locareaid']]."\n";
	    $str.="机柜名: ".$racks_arr[$check_info['rackid']]."\n";
	    
	    if ($check_info['rackid']!=1753) {
	        if (intval($check_info['rackposition'])>0 && intval($check_info['usize'])>0) {
	            $index = intval($check_info['rackposition']);
	            $temp_arr = array();
	            for ($i=0;$i<intval($check_info['usize']);$i++){
	                $a = $index+$i;
	                if ($i==0) {
	                    $temp_arr[]=$a;
	                }
	                if ($i==intval($check_info['usize'])-1) {
	                    $temp_arr[]=$a;
	                }
	            }
	            $sss = implode('至', $temp_arr);
	            $str.="所占U: ".$sss."U\n";
	        }
	    }
	    
	    echo $str;die;
	}
	
	
	public function form(){
	    $sn = $_REQUEST['sn'];
	    $common_name =  $_REQUEST['common_name'];
	    $model =  $_REQUEST['model'];
	    $usize = intval($_REQUEST['usize']) ;
	    $index = intval($_REQUEST['index']);
	    $cindex = intval($_REQUEST['cindex']);
	    $dindex = intval($_REQUEST['dindex']);
	    
	    $agents = M('agents')->where("type=3")->select();
	    $depart = M('department')->select();
	    $itemtypes = M('itemtype')->select();
	    $agents_ids = deal_ids($agents);
	    $depart_ids = deal_ids($depart);
	    $itemtypes_ids = deal_ids($itemtypes);
	    
	    
	    $data['itemtypeid'] = $itemtypes_ids[$index];
	    $data['manufacturerid'] = $agents_ids[$cindex];
	    $data['common_name'] = $common_name;
	    $data['model'] = $model;
	    $data['sn'] = $sn;
	    $data['usize'] = $usize;
	    $data['depart_id'] =  $depart_ids[$dindex];
	    

	    $data['status'] = 2; //默认入库
	    $data['rackid'] = 1753;
	    $data['locareaid'] = 1752;
	    $data['locationid'] = 1751;
	    
	    $checklist=M('ipdb_items')->where('sn = "'.$sn.'" ')->select();
	    if (empty($checklist)){
	        $list= M('ipdb_items')->data($data)->add();
	        if (!empty($list)){
	            echo "OK";
	            exit();
	        }
	    }else{
	        echo "CH";
	        exit();
	    }
	    
	    
	}
}

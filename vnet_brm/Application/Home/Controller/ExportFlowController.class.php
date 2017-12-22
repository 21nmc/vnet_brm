<?php
namespace Home\Controller;
use Think\Controller;
class  ExportFlowController extends HomeController {
    //出口带宽分布
    public function index(){
        $types = M('resource_zabbix_type');
        $ports = M('resource_zabbix');
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        
        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        $type_arr = array();
        $type_list = $types->where(" depart_id in (".$depart_id.") ")->select();
        foreach ($type_list as $one){
            $temp = array();
            $temp['name'] = $one['type_name'];
            $temp['id'] = $one['id'];
            $temp['depart_id'] = $one['depart_id'];
            $temp['pay_bandwidth'] = $one['pay_bandwidth'];
            $type_id = $one['id'];
            $port_list = $ports->where("type_id = $type_id")->select();
            $in_sum = 0;
            $out_sum = 0;
            foreach ($port_list as $o){
                $in = intval($o['in']);
                $out = intval($o['out']);
                $flag = $o['flag_change'];
                if ($flag == "1") {
                    $in_sum+= $out;
                    $out_sum+= $in;
                }else{
                    $in_sum+= $in;
                    $out_sum+= $out;
                }
            }
            $temp['in_sum'] = round(($in_sum/(1024*1024)),3);
            $temp['out_sum'] = round(($out_sum/(1024*1024)),3);
            $type_arr[]=$temp;
        }

        
        $this->assign("type_list",$type_arr);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }






    public function index2(){
        $types = M('resource_zabbix_type');
        $Resource_zabbix2 = M('resource_zabbix2');
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        
        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        $type_arr = array();
        $type_list = $types->where(" depart_id in (".$depart_id.") ")->select();
        foreach ($type_list as $one){
            $temp = array();
            $temp['name'] = $one['type_name'];
            $temp['id'] = $one['id'];
            $temp['depart_id'] = $one['depart_id'];
            $temp['pay_bandwidth'] = $one['pay_bandwidth'];
            $type_id = $one['id'];
            $port_list = $Resource_zabbix2->where("type_id = $type_id ")->select();// status=0表示参与计算统计状态
            $in_sum = 0;
            $out_sum = 0;
            foreach ($port_list as $o){
                $in = intval($o['in']);
                $out = intval($o['out']);
            }
            $temp['in_sum'] = round(($in_sum/(1024*1024)),3);
            $temp['out_sum'] = round(($out_sum/(1024*1024)),3);
            $type_arr[]=$temp;
        }

        
        $this->assign("type_list",$type_arr);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }



    public function city(){
        $types = M('city');
   
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $type_list = $types->order('id desc')->select();

        $this->assign("type_list",$type_list);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }


    public function product(){
        $types = M('week_product');
     
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $type_list = $types->order('id desc')->select();

        $this->assign("type_list",$type_list);
        $this->assign('url_flag','chukou'); //left flag
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
    


    public function port_list2(){
    
        $types = M('resource_zabbix_type');
        $Resource_zabbix2 = M('resource_zabbix2');
        
        
        $type_id = $_REQUEST['type_id'];
        $type_info = $types->where("id = $type_id")->find();
        $port_list = $Resource_zabbix2->where("type_id = $type_id")->order('id desc')->select();
        
        $depart_id = $type_info['depart_id'];
        $type_list = $types->where("depart_id = $depart_id")->select();
        
        $this->assign("type_list",$type_list);
        $this->assign("depart_id",$depart_id);
        $this->assign("port_list",$port_list);
        $this->assign('url_flag','chukou'); //left flag
        $this->assign("type_id",$type_id);
        $this->display();
    }


    public function movedel_port(){

        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);
        
        $types = M('resource_zabbix_type');
        $Resource_zabbix = M('resource_zabbix');
        $id = $_REQUEST['id'];
        $type_id = $_REQUEST['type_id'];

        empty($id) && $this->error('请输入ID');
    
        $data['status'] = 1;
        $alist=$Resource_zabbix->where("id = $id")->save($data);
        if (!empty($alist)){
            $url = U("ExportFlow/port_list?type_id=".$type_id);
            $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
            echo $url;
            exit();
        }else{
            $url = U("ExportFlow/port_list?type_id=".$type_id);
            $url = '<script type="text/javascript" >alert("更新失败");window.location.href="'.$url.'"; </script>';
            echo $url;
            exit();
        }

        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }


    public function moveadd_port(){

        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);
        
        $types = M('resource_zabbix_type');
        $Resource_zabbix = M('resource_zabbix');
        $id = $_REQUEST['id'];
        $type_id = $_REQUEST['type_id'];

        empty($id) && $this->error('请输入ID');
    
        $data['status'] = 0;
        $alist=$Resource_zabbix->where("id = $id")->save($data);
        if (!empty($alist)){
            $url = U("ExportFlow/port_list?type_id=".$type_id);
            $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
            echo $url;
            exit();
        }else{
            $url = U("ExportFlow/port_list?type_id=".$type_id);
            $url = '<script type="text/javascript" >alert("更新失败");window.location.href="'.$url.'"; </script>';
            echo $url;
            exit();
        }

        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }






    public function movedel_port2(){

        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);
        
        $types = M('resource_zabbix_type');
        $Resource_zabbix2 = M('resource_zabbix2');
        $id = $_REQUEST['id'];
        $type_id = $_REQUEST['type_id'];

        empty($id) && $this->error('请输入ID');

        $data['status'] = 1;
        $alist=$Resource_zabbix2->where("id = $id")->save($data);
        if (!empty($alist)){
            $url = U("ExportFlow/port_list2?type_id=".$type_id);
            $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
            echo $url;
            exit();
        }else{
            $url = U("ExportFlow/port_list2?type_id=".$type_id);
            $url = '<script type="text/javascript" >alert("更新失败");window.location.href="'.$url.'"; </script>';
            echo $url;
            exit();
        }

        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }


    public function moveadd_port2(){

        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);
        
        $types = M('resource_zabbix_type');
        $Resource_zabbix2 = M('resource_zabbix2');
        $id = $_REQUEST['id'];
        $type_id = $_REQUEST['type_id'];

        empty($id) && $this->error('请输入ID');
    
        $data['status'] = 0;
        $alist=$Resource_zabbix2->where("id = $id")->save($data);
        if (!empty($alist)){
            $url = U("ExportFlow/port_list2?type_id=".$type_id);
            $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
            echo $url;
            exit();
        }else{
            $url = U("ExportFlow/port_list2?type_id=".$type_id);
            $url = '<script type="text/javascript" >alert("更新失败");window.location.href="'.$url.'"; </script>';
            echo $url;
            exit();
        }

        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }
































    public function add_port(){
        $types = M('resource_zabbix_type');
        $ports = M('resource_zabbix');
        $Traffic_portlist = M('traffic_portlist');
        
        $depart_id = $_REQUEST['depart_id'];
        $type_list = $types->where("depart_id = $depart_id")->select();
        
        if ( IS_POST ) {
            $device_name = $_REQUEST['device_name'];
            $ip = $_REQUEST['ip'];
            $port = $_REQUEST['port'];
            $flag_change = $_REQUEST['flag_change'];
            
            $device_find = M('ipdb_items')->where("ipv4 = '$ip'")->find();
            empty($device_find) && $this->error('设备在本系统不存在，请检查');
            
            $type_id = $_REQUEST['type_id'];
            $type_info = $types->where("id = $type_id")->find();
            $list = $Traffic_portlist->where('host = "'.$ip.'" and portname="'.$port.'" ')->select();
            $hostid=$list[0]['hostid'];
            $initemid=$list[0]['initemid'];
            $outitemid=$list[0]['outitemid'];
            
            empty($hostid) && $this->error('没加监控！');
            empty($initemid) && $this->error('没有授权或轮询为获取参数！');
            empty($outitemid) && $this->error('没有授权或轮询为获取参数！');
            
            
            $data['type_id'] = $type_id;
            $data['port'] = $port;
            $data['ip'] = $ip;
            $data['device_name'] = $device_name.' '.$port;
            $data['ip'] = $ip;
            $data['hostid'] = $hostid;
            $data['initemid'] = $initemid;
            $data['outitemid'] = $outitemid;
            $data['depart_id'] = $depart_id;
            $data['flag_change'] = $flag_change;
            $data['flag_type'] = $type_info["type_name"];
            $data['type_name'] = $type_info["type_name"];
        
            $checklist=$ports->where('port = "'.$port.'" and ip = "'.$ip.'" and device_name = "'.$device_name.'"')->select();
            if (empty($checklist)){
                $list=$ports->data($data)->add();
                if (!empty($list)){
                    $url = U("ExportFlow/port_list?type_id=".$type_id);
                    $url = '<script type="text/javascript" >alert("添加成功");window.location.href="'.$url.'"; </script>';
                    $this->show($url);
                    exit();
                }
            }else{
                $this->error("数据重复添加");exit();
            }
        
        
        }
        
        
        $this->assign("type_list",$type_list);
        $this->assign('depart_id',$depart_id); 
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }
    
    public function del_port(){
    
        $ports = M('resource_zabbix');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $ports->where($where)->delete();
        if ($del) {
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
    
    public function edit_port(){
        $types = M('resource_zabbix_type');
        $ports = M('resource_zabbix');
        $Traffic_portlist = M('traffic_portlist');
    
        $id = $_REQUEST['id'];
        $port_info = $ports->where("id = $id")->find();
        $depart_id = $port_info['depart_id'];
        $type_list = $types->where("depart_id = $depart_id")->select();
    
        if ( IS_POST ) {
            $device_name = $_REQUEST['device_name'];
            $ip = $_REQUEST['ip'];
            $port = $_REQUEST['port'];
            $flag_change = $_REQUEST['flag_change'];
    
            $device_find = M('ipdb_items')->where("ipv4 = '$ip'")->find();
            empty($device_find) && $this->error('设备在本系统不存在，请检查');
    
            $type_id = $_REQUEST['type_id'];
            $type_info = $types->where("id = $type_id")->find();
            $list = $Traffic_portlist->where('host = "'.$ip.'" and portname="'.$port.'" ')->select();
            $hostid=$list[0]['hostid'];
            $initemid=$list[0]['initemid'];
            $outitemid=$list[0]['outitemid'];
    
            empty($hostid) && $this->error('没加监控！');
            empty($initemid) && $this->error('没有授权或轮询为获取参数！');
            empty($outitemid) && $this->error('没有授权或轮询为获取参数！');
    
    
            $data['type_id'] = $type_id;
            $data['port'] = $port;
            $data['ip'] = $ip;
            $device_name_list = explode(" ", $device_name);
            $data['device_name'] = $device_name_list[0].' '.$port;
            $data['ip'] = $ip;
            $data['hostid'] = $hostid;
            $data['initemid'] = $initemid;
            $data['outitemid'] = $outitemid;
            $data['depart_id'] = $depart_id;
            $data['flag_change'] = $flag_change;
            $data['flag_type'] = $type_info["type_name"];
            $data['type_name'] = $type_info["type_name"];

    
            $checklist=$ports->where('port = "'.$port.'" and ip = "'.$ip.'" and device_name = "'.$device_name.'"')->select();
            if (empty($checklist)){
                $list=$ports->where("id = $id")->save($data);
                if (!empty($list)){
                    $url = U("ExportFlow/port_list?type_id=".$type_id);
                    $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
                    $this->show($url);
                    exit();
                }
            }else{
                $this->error("更新失败");exit();
            }
    
    
        }
    
        $this->assign("port_info",$port_info);
        $this->assign("type_list",$type_list);
        $this->assign('depart_id',$depart_id);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }
    












    public function add_port2(){
        $types = M('resource_zabbix_type');
        $City = M('city');
        $Week_product = M('week_product');
        $ports = M('resource_zabbix2');
        $Traffic_portlist = M('traffic_portlist');
        $Room = M('room');
        
        $depart_id = $_REQUEST['depart_id'];
        $type_list = $types->where("depart_id = $depart_id")->select();
        
        $city_list=$City->order('id asc')->select();
        $product_list=$Week_product->order('id asc')->select();
        $room_list = $Room->select();


        if ( IS_POST ) {
            $device_name = $_REQUEST['device_name'];
            $ip = $_REQUEST['ip'];
            $port = $_REQUEST['port'];
            $flag_change = $_REQUEST['flag_change'];
            $city_code = $_REQUEST['city_code'];
            $product_code = $_REQUEST['product_code'];
            $roomcode = $_REQUEST['roomcode'];

            //$device_find = M('ipdb_items')->where("ipv4 = '$ip'")->find();
            //empty($device_find) && $this->error('设备在本系统不存在，请检查');
            
            $type_id = $_REQUEST['type_id'];
            $type_info = $types->where("id = $type_id")->find();
            $list = $Traffic_portlist->where('host = "'.$ip.'" and portname="'.$port.'" ')->select();
            $hostid=$list[0]['hostid'];
            $initemid=$list[0]['initemid'];
            $outitemid=$list[0]['outitemid'];
            
            //($hostid) && $this->error('没加监控！');
            //empty($initemid) && $this->error('没有授权或轮询为获取参数！');
            //empty($outitemid) && $this->error('没有授权或轮询为获取参数！');
            empty($city_code) && $this->error('城市参数不能为空！');
            empty($product_code) && $this->error('产品编码参数不能为空！');
            empty($roomcode) && $this->error('机房编码参数不能为空！');

            $data['roomcode'] = $roomcode;
            $data['citycode'] = $city_code;
            $data['productcode'] = $product_code;
            $data['type_id'] = $type_id;
            $data['port'] = $port;
            $data['ip'] = $ip;
            $data['device_name'] = $device_name.' '.$port;
            $data['ip'] = $ip;
            //$data['hostid'] = $hostid;
            //$data['initemid'] = $initemid;
            //$data['outitemid'] = $outitemid;
            $data['depart_id'] = $depart_id;
            $data['flag_change'] = $flag_change;
            $data['flag_type'] = $type_info["type_name"];
            $data['type_name'] = $type_info["type_name"];
        
            $checklist=$ports->where('port = "'.$port.'" and ip = "'.$ip.'" and device_name = "'.$device_name.'"')->select();
            if (empty($checklist)){
                $list=$ports->data($data)->add();
                if (!empty($list)){
                    $url = U("ExportFlow/port_list2?type_id=".$type_id);
                    $url = '<script type="text/javascript" >alert("添加成功");window.location.href="'.$url.'"; </script>';
                    echo $url;
                    exit();
                }
            }else{
                $this->error("数据重复添加");exit();
            }
        
        
        }
        
        
        $this->assign("room_list",$room_list);
        $this->assign("type_list",$type_list);
        $this->assign("city_list",$city_list);
        $this->assign("product_list",$product_list);
        $this->assign('depart_id',$depart_id); 
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }
    
    public function del_port2(){
    
        $ports = M('resource_zabbix2');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $ports->where($where)->delete();
        if ($del) {
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
    
    public function edit_port2(){
        $types = M('resource_zabbix_type');
        $Traffic_portlist = M('traffic_portlist');
        $resource= M('resource_zabbix2');
        $Week_product = M('week_product');
        $Room = M('room');

        $City = M('city');

        $id = $_REQUEST['id'];
        $port_info = $resource->where("id = $id")->find();
        $depart_id = $port_info['depart_id'];
        $type_list = $types->where("depart_id = $depart_id")->select();
        $resource_list = $resource->select();
        $room_list = $Room->select();

        $city_list=$City->order('id asc')->select();
        $product_list=$Week_product->order('id asc')->select();


        if ( IS_POST ) {
            // $device_name = $_REQUEST['device_name'];
            // $rid = $_REQUEST['rid'];
            // $ip = $_REQUEST['ip'];
            // $port = $_REQUEST['port'];
            // $flag_change = $_REQUEST['flag_change'];

            //$device_find = M('ipdb_items')->where("ipv4 = '$ip'")->find();
            //empty($device_find) && $this->error('设备在本系统不存在，请检查');
    
            $type_id = $_REQUEST['type_id'];
            $id = $_REQUEST['id'];
            $rid = $_REQUEST['rid'];
            $roomcode  = $_REQUEST['roomcode'];
            $city_code = $_REQUEST['citycode'];
            $product_code = $_REQUEST['productcode'];
            //$type_info = $types->where("id = $type_id")->find();
            // $list = $Traffic_portlist->where('host = "'.$ip.'" and portname="'.$port.'" ')->select();
            // $hostid=$list[0]['hostid'];
            // $initemid=$list[0]['initemid'];
            // $outitemid=$list[0]['outitemid'];
    
            //empty($hostid) && $this->error('没加监控！');
            //empty($initemid) && $this->error('没有授权或轮询为获取参数！');
            //empty($outitemid) && $this->error('没有授权或轮询为获取参数！');
    
             empty($city_code) && $this->error('城市参数不能为空！');
             empty($product_code) && $this->error('产品编码参数不能为空！');
             empty($roomcode) && $this->error('机房编码参数不能为空！');

           // var_dump($_REQUEST);
            $type_info = $types->where("id = $type_id")->find();
            $type_name=$type_info['type_name'];

            $data['citycode'] = $city_code;
            $data['productcode'] = $product_code;
            $data['roomcode'] = $roomcode;
            $data['type_id'] = $type_id;
            $data['type_name'] = $type_name;

            

            // $data['type_id'] = $type_id;
            // $data['port'] = $port;
            // $data['ip'] = $ip;
            // $device_name_list = explode(" ", $device_name);
            // $data['device_name'] = $device_name_list[0].' '.$port;
            // $data['ip'] = $ip;
            // $data['hostid'] = $hostid;
            // $data['initemid'] = $initemid;port_list2
            // $data['outitemid'] = $outitemid;
            // $data['depart_id'] = $depart_id;
            // $data['flag_change'] = $fport_list2ag_change;
            // $data['flag_type'] = $type_info["type_name"];
            // $data['type_name'] = $type_info["type_name"];

         
            //$checklist=$ports->where('port = "'.$port.'" and ip = "'.$ip.'" and device_name = "'.$device_name.'"')->select();
            //if (empty($checklist)){

                $list=$resource->where("id = $id")->save($data);
                if (!empty($list)){
                    $url = U("ExportFlow/port_list2?type_id=".$type_id);
                    $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
                    echo $url;
                    exit();
                }

            //}else{
             //   $this->error("更新失败");exit();
           // }
    
    
       }
     //var_dump(IS_POST);$id
        $this->assign("product_list",$product_list);
        $this->assign("city_list",$city_list);
        $this->assign("port_info",$port_info);
        $this->assign("type_list",$type_list);
        $this->assign('depart_id',$depart_id);
        $this->assign('room_list',$room_list);
        $this->assign('resource_list',$resource_list);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }
    




















    public function flow_type(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        $types = M('resource_zabbix_type');
        $top = $types->where(" depart_id in (".$depart_id.")")->select();


/*        $arr = array();
        foreach ($top as $one){
            $temp['top'] = $one;
            $fid = $one['id'];
            $sub = $types->where("fid = $fid")->select();
            $temp['sub'] = $sub;
            $arr[] = $temp;
        }*/
        
        $this->assign("list",$top);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }
    
    public function flow_type_new(){
        $types = M('resource_zabbix_type');
        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        if ( IS_POST ) {
            
            $type_name = $_REQUEST['type_name'];
            $depart_id = $_REQUEST['depart_id'];
            $fid = $_REQUEST['fid'];
            $pay_bandwidth = $_REQUEST['pay_bandwidth'];
        
            empty($type_name) && $this->error('请输入类型名称');
            empty($depart_id) && $this->error('请输入区域');
            
            $data['type_name'] = $type_name;
            $data['depart_id'] = $depart_id;
            $data['fid'] = $fid;
            if ($fid == "0") {
                $data['pay_bandwidth'] = $pay_bandwidth;
            }
            $alist=$types->data($data)->add();
            if (!empty($alist)){
                //echo "资产添加成功";
                $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("ExportFlow/flow_type")}"; </script>');
                exit();
            }
        
        }
        $fids = $types->where("depart_id in (".$depart_id.")")->select();
        $this->assign("list",$fids);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }

     public function add_city(){
        $types = M('city');
        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        if ( IS_POST ) {
            
            $ctid = $_REQUEST['ctid'];
            $name = $_REQUEST['name'];
            // $fid = $_REQUEST['fid'];
            // $pay_bandwidth = $_REQUEST['pay_bandwidth'];
        
            empty($ctid) && $this->error('请输入产品id');
            empty($name) && $this->error('请输入产品名');
            
            $data['citycode'] = $ctid;
            $data['name'] = $name;
            $data['fid'] = $fid;
            // if ($fid == "0") {
            //     $data['pay_bandwidth'] = $pay_bandwidth;
            // }
            $alist=$types->data($data)->add();
            if (!empty($alist)){
                //echo "资产添加成功";
                $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("ExportFlow/city")}"; </script>');
                exit();
            }
        
        }
        //$fids = $types->where("depart_id in (".$depart_id.")")->select();
        // $this->assign("list",$fids);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }


     public function add_product(){
        $types = M('product');
        $uid        =   is_login();
       $demo = M('room');
        $demo2 = M('city');
         $demo3 = M('department');


        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);
    
          $room_list = $demo->select();
          $city_list = $demo2->select();
          $department_list = $demo3->select();

        if ( IS_POST ) {
            
            $title = $_REQUEST['title'];
         
        
            empty($title) && $this->error('请输入产品名称');
         
            
            $data['title'] = $title;
           
            $alist=$types->data($data)->add();
            if (!empty($alist)){
                //echo "资产添加成功";
                $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("ExportFlow/product")}"; </script>');
                exit();
            }
        
        }
        // $fids = $types->where("depart_id in (".$depart_id.")")->select();
        // $this->assign("list",$fids);
        $this->assign("room_list",$room_list);
        $this->assign("city_list",$city_list);
        $this->assign("department_list",$department_list);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }
    
    
    public function edit_type(){

        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);
        
        $types = M('resource_zabbix_type');
        $ports = M('resource_zabbix');
        $id = $_REQUEST['id'];
        $type_info = $types->where("id = $id")->find();

   
        if ( IS_POST ) {
    
            $type_name = $_REQUEST['type_name'];
            $depart_id = $_REQUEST['depart_id'];
            $fid = $_REQUEST['fid'];
            $id = $_REQUEST['id'];
            $pay_bandwidth = $_REQUEST['pay_bandwidth'];
    
            empty($type_name) && $this->error('请输入类型名称');
            empty($depart_id) && $this->error('请输入区域');
            empty($id) && $this->error('请输入ID');
    
            $data['type_name'] = $type_name;
            $data['depart_id'] = $depart_id;
            //$data['fid'] = $fid;
            //if ($fid == "0") {
                $data['pay_bandwidth'] = $pay_bandwidth;
            //}
            $alist=$types->where("id = $id")->save($data);
            if (!empty($alist)){
                //echo "资产添加成功";
                $data_now['flag_type'] = $type_name;
                $ports->where("type_id = $id")->save($data_now);
                //$this->success("更新成功");
                $url = U("ExportFlow/flow_type");
                $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }else{
                $url = U("ExportFlow/flow_type");
                $url = '<script type="text/javascript" >alert("更新失败");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }
    
        }
        $fids = $types->where("depart_id in (".$depart_id.")")->select();
        $this->assign("list",$fids);
        $this->assign("type_info",$type_info);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }
    
public function edit_city(){

        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);
        
        $types = M('city');
        $ports = M('resource_zabbix');
        $id = $_REQUEST['id'];
        $type_info = $types->where("id = $id")->find();

   
        if ( IS_POST ) {
    
            $ctid = $_REQUEST['ctid'];
            $name = $_REQUEST['name'];
            // $fid = $_REQUEST['fid'];
            // $id = $_REQUEST['id'];
            // $pay_bandwidth = $_REQUEST['pay_bandwidth'];
    
            empty($ctid) && $this->error('请输入城市id');
            // empty($depart_id) && $this->error('请输入区域');
            empty($name) && $this->error('请输入城市名');
    
            $data['citycode'] = $ctid;
            $data['name'] = $name;
            //$data['fid'] = $fid;
            //if ($fid == "0") {
                // $data['pay_bandwidth'] = $pay_bandwidth;
            //}
            $alist=$types->where("id = $id")->save($data);
            if (!empty($alist)){
                //echo "资产添加成功";
                $data_now['flag_type'] = $type_name;
                $ports->where("id = $id")->save($data_now);
                //$this->success("更新成功");
                $url = U("ExportFlow/city");
                $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }else{
                $url = U("ExportFlow/city");
                $url = '<script type="text/javascript" >alert("更新失败");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }
    
        }
        $fids = $types->where("id in (".$depart_id.")")->select();
        $this->assign("list",$fids);
        $this->assign("type_info",$type_info);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }
    
public function edit_product(){

        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);
        
        $types = M('product');
        $ports = M('resource_zabbix');
        $id = $_REQUEST['id'];
        $type_info = $types->where("id = $id")->find();

   
        if ( IS_POST ) {
    
            $title = $_REQUEST['title'];
          
            // $fid = $_REQUEST['fid'];
            // $id = $_REQUEST['id'];
            // $pay_bandwidth = $_REQUEST['pay_bandwidth'];
    
            empty($title) && $this->error('请输入产品名');
            // empty($depart_id) && $this->error('请输入区域');
            
    
            $data['title'] = $title;
           
            //$data['fid'] = $fid;
            //if ($fid == "0") {
                // $data['pay_bandwidth'] = $pay_bandwidth;
            //}
            $alist=$types->where("id = $id")->save($data);
            if (!empty($alist)){
                //echo "资产添加成功";
                $data_now['flag_type'] = $type_name;
                $ports->where("id = $id")->save($data_now);
                //$this->success("更新成功");
                $url = U("ExportFlow/product");
                $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }else{
                $url = U("ExportFlow/product");
                $url = '<script type="text/javascript" >alert("更新失败");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }
    
        }
        $fids = $types->where("id in (".$depart_id.")")->select();
        $this->assign("list",$fids);
        $this->assign("type_info",$type_info);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }



    public function del_city(){
    
        $types = M('city');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $types->where($where)->delete();
        if ($del) {
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }


     public function del_product(){
    
        $types = M('product');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $types->where($where)->delete();
        if ($del) {
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }



    public function del_type(){
    
        $types = M('resource_zabbix_type');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $types->where($where)->delete();
        if ($del) {
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
    
    
    public function idc_flow(){
        $idc = M('ipdb_locations')->select();
    }
    
    



    public function week(){

        $Week = M('week');
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        $mytime=mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));//获取时间戳  
        $stime=date("Y-m-d", strtotime("-7 day"));
        //$stime=strtotime($stime." 00:00:00"); 
        $etime=date("Y-m-d");
        //$etime=strtotime($etime." 00:00:00"); 

        $week_list = $Week->where("depart_id in (".$depart_id.")  ")->select(); // and stime>='".$stime."'  and etime<='".$etime."'
        for($i=0;$i<count($week_list);$i++){
            $bandwidth=$week_list[$i]['bandwidth'];
            $pay_bandwidth=$week_list[$i]['pay_bandwidth'];
            $week_list[$i]['shiyonglv']=round(($bandwidth/$pay_bandwidth)*100,2);

        }

        $this->assign("week_list",$week_list);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();

    }




    public function doubleweek(){

        $Doubleweek = M('doubleweek');
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        $mytime=mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));//获取时间戳  
        $stime=date("Y-m-d", strtotime("-7 day"));
        //$stime=strtotime($stime." 00:00:00"); 
        $etime=date("Y-m-d");
        //$etime=strtotime($etime." 00:00:00"); 

        $week_list = $Doubleweek->where("depart_id in (".$depart_id.")  ")->select(); // and stime>='".$stime."'  and etime<='".$etime."'
        for($i=0;$i<count($week_list);$i++){
            $bandwidth=$week_list[$i]['bandwidth'];
            $pay_bandwidth=$week_list[$i]['pay_bandwidth'];
            $week_list[$i]['shiyonglv']=round(($bandwidth/$pay_bandwidth)*100,2);

        }

        $this->assign("week_list",$week_list);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();

    }




    public function month(){

        $Month = M('month');
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        $mytime=mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));//获取时间戳  
        $stime=date("Y-m-d", strtotime("-7 day"));
        //$stime=strtotime($stime." 00:00:00"); 
        $etime=date("Y-m-d");
        //$etime=strtotime($etime." 00:00:00"); 

        $week_list = $Month->where("depart_id in (".$depart_id.")  ")->select(); // and stime>='".$stime."'  and etime<='".$etime."'
        for($i=0;$i<count($week_list);$i++){
            $bandwidth=$week_list[$i]['bandwidth'];
            $pay_bandwidth=$week_list[$i]['pay_bandwidth'];
            $week_list[$i]['shiyonglv']=round(($bandwidth/$pay_bandwidth)*100,2);

        }

        $this->assign("week_list",$week_list);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();

    }




    public function mis_month(){

        $Mis_month = M('mis_month');
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        $mytime=mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));//获取时间戳  
        $stime=date("Y-m-d", strtotime("-7 day"));
        //$stime=strtotime($stime." 00:00:00"); 
        $etime=date("Y-m-d");
        //$etime=strtotime($etime." 00:00:00"); 

        $week_list = $Mis_month->where("depart_id in (".$depart_id.")  ")->select(); // and stime>='".$stime."'  and etime<='".$etime."'
        for($i=0;$i<count($week_list);$i++){
            $bandwidth=$week_list[$i]['bandwidth'];
            $pay_bandwidth=$week_list[$i]['pay_bandwidth'];
            $week_list[$i]['shiyonglv']=round(($bandwidth/$pay_bandwidth)*100,2);

        }

        $this->assign("week_list",$week_list);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();

    }




    public function edit_week(){
        $Week = M('week');
        $id = $_REQUEST['id'];
        $week_info = $Week->where("id = $id")->find();
    
        if ( IS_POST ) {
    
            $id = $_REQUEST['id'];
            $pay_bandwidth = $_REQUEST['pay_bandwidth'];
            empty($id) && $this->error('请输入ID');
    
            $data['pay_bandwidth'] = $pay_bandwidth;
            $Week->where("id = $id")->save($data);
            //$this->success("更新成功");
            $url = U("ExportFlow/week");
            $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
            echo $url;
            exit();
        }

        $this->assign("list",$fids);
        $this->assign("week_info",$week_info);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }



    public function auto_week(){

        $Week_history = M('week_history');
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        
        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);
        
        $end = $_REQUEST['end'];
        $start = $_REQUEST['start'];


        if ( IS_POST ) {
            if(empty($start) && empty($end)){$this->error('请输入正确的时间周期!');}
            $week_list = $Week_history->where("depart_id in (".$depart_id.") and stime>='".$start."'  and etime<='".$end."'  ")->group('type_id')->field('stime,etime,type_id,type_name,depart_id,sum(bandwidth) as all_bandwidth,sum(pay_bandwidth) as all_pay_bandwidth')->select(); 
            for($i=0;$i<count($week_list);$i++){
                $all_pay_bandwidth=$week_list[$i]['all_pay_bandwidth'];
                $all_bandwidth=$week_list[$i]['all_bandwidth'];


                $week_list[$i]['all_bandwidth']=$week_list[$i]['all_bandwidth'];
                $week_list[$i]['all_pay_bandwidth']=$week_list[$i]['all_pay_bandwidth'];

                $week_list[$i]['shiyonglv']=round(($bandwidth/$pay_bandwidth)*100,2);
            }

        }
        $this->assign("end",$_REQUEST['end']);
        $this->assign("start",$_REQUEST['start']);
        $this->assign("week_list",$week_list);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();

    }






    public function history(){

        $Week_history = M('week_history');
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        
        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        
        $end = $_REQUEST['end'];
        $start = $_REQUEST['start'];
        $report_type = $_REQUEST['report_type'];

        if ( IS_POST ) {
            if(empty($start) && empty($end)){$this->error('请输入正确的时间周期!');}
            if(empty($report_type)){$this->error('请选择报表类别!');}

            if(empty($report_type)){
                $where="depart_id in (".$depart_id.") and stime>='".$start."'  and etime<='".$end."'  ";
                $week_list = $Week_history->where($where)->select();
            }else{
                $where="report_type='".$report_type."' and depart_id in (".$depart_id.") and stime>='".$start."'  and etime<='".$end."'  ";
                $week_list = $Week_history->where($where)->field('avg(bandwidth) as avgbandwidth,bandwidth,pay_bandwidth,type_name,stime,etime,id')->group('type_name')->select();                
            }
//echo "report_type='".$report_type."' and depart_id in (".$depart_id.") and stime>='".$start."'  and etime<='".$end."'  ";
            //echo $where;
            for($i=0;$i<count($week_list);$i++){
                $bandwidth=$week_list[$i]['bandwidth'];
                $pay_bandwidth=$week_list[$i]['pay_bandwidth'];
                $avgbandwidth=$week_list[$i]['avgbandwidth'];

                $week_list[$i]['shiyonglv']=round(($bandwidth/$pay_bandwidth)*100,2);
                $week_list[$i]['shiyonglvavg']=round(($avgbandwidth/$pay_bandwidth)*100,2);


            }
        }

        $this->assign("end",$_REQUEST['end']);
        $this->assign("start",$_REQUEST['start']);
        $this->assign("report_type",$_REQUEST['report_type']);
        $this->assign("week_list",$week_list);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();

    }




    public function day(){

        $Week_day = M('week_day');
        $Week_data = M('week_data');
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        $mytime=mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));//获取时间戳  
        $stime=date("Y-m-d", strtotime("-1 day"));
        //$stime=strtotime($stime." 00:00:00"); 
        $etime=date("Y-m-d");
        //$etime=strtotime($etime." 00:00:00"); 
//echo "depart_id in (".$depart_id.")  and stime>='".$stime."'  and etime<'".$etime."' ";
        $week_list = $Week_day->where("depart_id in (".$depart_id.")  and stime>='".$stime."'  and etime<='".$etime."' ")->select();
        for($i=0;$i<count($week_list);$i++){
            $bandwidth=$week_list[$i]['bandwidth'];
            $type_id=$week_list[$i]['type_id'];
            //$week_datalist = $Week_data->where("daytime='".$stime."'  and  type_id='".$type_id."' ")->select();
            
           // $week_list[$i]['bandwidth']=;

            $pay_bandwidth=$week_list[$i]['pay_bandwidth'];
            $week_list[$i]['shiyonglv']=round(($bandwidth/$pay_bandwidth)*100,2);
        }

        $this->assign("week_list",$week_list);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();

    }





//华北机房带宽产品统计
    public function huabei_product(){
        $types = M('resource_zabbix_type');
        $ports = M('resource_zabbix');
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        
        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        $type_arr = array();
        $type_list = $types->where(" depart_id in (".$depart_id.") ")->select();
        foreach ($type_list as $one){
            $temp = array();
            $temp['name'] = $one['type_name'];
            $temp['id'] = $one['id'];
            $temp['depart_id'] = $one['depart_id'];
            $temp['pay_bandwidth'] = $one['pay_bandwidth'];
            $type_id = $one['id'];
            $port_list = $ports->where("type_id = $type_id")->select();
            $in_sum = 0;
            $out_sum = 0;
            foreach ($port_list as $o){
                $in = intval($o['in']);
                $out = intval($o['out']);
                $flag = $o['flag_change'];
                if ($flag == "1") {
                    $in_sum+= $out;
                    $out_sum+= $in;
                }else{
                    $in_sum+= $in;
                    $out_sum+= $out;
                }
            }
            $temp['in_sum'] = round(($in_sum/(1024*1024)),3);
            $temp['out_sum'] = round(($out_sum/(1024*1024)),3);
            $type_arr[]=$temp;
        }

        
        $this->assign("type_list",$type_arr);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }








/**出口端口管理**/
public function portmanger() {

    $uid        =   is_login();
    
    $depart_ids=get_hostgroup_by_uid($uid);
    $depart_id=implode(",",$depart_ids);


    $Resource_zabbix2 = M('resource_zabbix2');
    $Resource_zabbix_type = M('resource_zabbix_type');
    $Week_port_config = M('week_port_config');
    
    $tempresource_zabbix2_id=$_REQUEST['resource_zabbix2_id'];
    $tempresource_zabbix_type_id=$_REQUEST['resource_zabbix_type_id'];
    $selfhostgroup_id=$_REQUEST['selfhostgroup_id'];


    $checkusergroupoption="";
    $usergrouplist=$Resource_zabbix_type->where(' depart_id in ('.$depart_id.') ')->order(' id desc')->select();
    for($i=0;$i<count($usergrouplist);$i++){
       $id=$usergrouplist[$i]['id'];
       $type_name=$usergrouplist[$i]['type_name'];
       if ($tempresource_zabbix_type_id==$id){
            $usergroupoption.='<option value="'.$id.'" selected="selected">'.$type_name.'</option>';
            $checkusergroupoption.='<option value="'.__ROOT__.'/index.php?s=/Home/ExportFlow/portmanger/resource_zabbix_type_id/'.$id.'.html" selected="selected">'.$type_name.'</option>';
       }else{
            $usergroupoption.='<option value="'.$id.'">'.$type_name.'</option>';
            $checkusergroupoption.='<option value="'.__ROOT__.'/index.php?s=/Home/ExportFlow/portmanger/resource_zabbix_type_id/'.$id.'.html">'.$type_name.'</option>';
       }
       
    }


    if (!empty($tempresource_zabbix_type_id)){


      //已经拥有系统访问权限

          $showgrouplist=$Week_port_config->where(' resource_zabbix_typeid ="'.$tempresource_zabbix_type_id.'" ')->select();
          $showallid='';
          for($j=0;$j<count($showgrouplist);$j++){
              $showhostgroup_id=$showgrouplist[$j]['resource_zabbix2id'];
              $showallid=$showallid.','.$showhostgroup_id;
          }
          $showallid=substr($showallid,1,strlen($showallid));

          $myhostgroupoption="";

      if (!empty($showallid)){

        
          $myhostgrouplist=$Resource_zabbix2->where(' id in ('.$showallid.') ')->select();
          for($i=0;$i<count($myhostgrouplist);$i++){
             $id=$myhostgrouplist[$i]['id'];
             $device_name=$myhostgrouplist[$i]['device_name'];
             if ($tempresource_zabbix_type_id==$id){
                  $myhostgroupoption.='<option value="'.$id.'" selected="selected">'.$device_name.'</option>';
             }else{
                  $myhostgroupoption.='<option value="'.$id.'">'.$device_name.'</option>';
             }
          }
    
      //全部系统群组排除已经存在的

          $exitmyhostgroupoption="";
          $exitmyhostgrouplist=$Resource_zabbix2->where(' id not in ('.$showallid.') and depart_id in ('.$depart_id.')')->select();
          //echo ' id not in ('.$showallid.') and depart_id in ('.$depart_id.')';
          for($i=0;$i<count($exitmyhostgrouplist);$i++){
             $id=$exitmyhostgrouplist[$i]['id'];
             $device_name=$exitmyhostgrouplist[$i]['device_name'];
             if ($tempresource_zabbix_type_id==$id){
                  $exitmyhostgroupoption.='<option value="'.$id.'" selected="selected">'.$device_name.'</option>';
             }else{
                  $exitmyhostgroupoption.='<option value="'.$id.'">'.$device_name.'</option>';
             }
          }
         
      }else{

          $exitmyhostgroupoption="";
          $exitmyhostgrouplist=$Resource_zabbix2->where('depart_id in ('.$depart_id.')')->select();

          for($i=0;$i<count($exitmyhostgrouplist);$i++){
             $id=$exitmyhostgrouplist[$i]['id'];
             $device_name=$exitmyhostgrouplist[$i]['device_name'];
             if ($tempresource_zabbix_type_id==$id){
                  $exitmyhostgroupoption.='<option value="'.$id.'" selected="selected">'.$device_name.'</option>';
             }else{
                  $exitmyhostgroupoption.='<option value="'.$id.'">'.$device_name.'</option>';
             }
          }


      }

  } 





    if(IS_POST){

        $tempresource_zabbix2_id=$_REQUEST['resource_zabbix2_id'];
        $tempresource_zabbix_type_id=$_REQUEST['resource_zabbix_type_id'];
        $selfhostgroup_id=$_REQUEST['selfhostgroup_id'];

        if(empty($tempresource_zabbix_type_id)){$this->error('端口类型不能为空!');}     
        if(empty($selfhostgroup_id)){$this->error('端口不能为空!');}

        for($i=0;$i<count($selfhostgroup_id);$i++){
            $dataList[]=array('resource_zabbix2id'=>$selfhostgroup_id[$i],'resource_zabbix_typeid'=>$tempresource_zabbix_type_id);
        }

        $Week_port_configlist=$Week_port_config->where('resource_zabbix_typeid="'.$tempresource_zabbix_type_id.'"')->select();
        for($i=0;$i<count($Week_port_configlist);$i++){
            $resource_zabbix2id=$Week_port_configlist[$i]['resource_zabbix2id'];
            if (!empty($resource_zabbix2id)){
                $firstupdatedata['status']=0;
                $Resource_zabbix2->where('id ="'.$resource_zabbix2id.'" ')->save($firstupdatedata);
            }            
        }        
        
        //删除
        $Week_port_config->where('resource_zabbix_typeid="'.$tempresource_zabbix_type_id.'"')->delete();
        if($Week_port_config->addAll($dataList)){
          
          for($i=0;$i<count($selfhostgroup_id);$i++){
            $updatedata['status']=1;
            $Resource_zabbix2->where('id ="'.$selfhostgroup_id[$i].'"')->save($updatedata);

          }

          //$url = U("ExportFlow/portmanger?resource_zabbix_type_id=".$tempresource_zabbix_type_id);
          //$url = '<script type="text/javascript" >alert("配置操作成功");window.location.href="'.$url.'"; </script>';
          $this->show('<script type="text/javascript" >alert("配置操作成功!");window.location.href="'.__ROOT__.'/index.php?s=/Home/ExportFlow/portmanger/resource_zabbix_type_id/'.$tempresource_zabbix_type_id.'.html"; </script>');
          exit();
        }else{
          $this->error('配置操作有误');
        }

    }







        $department_arr = $Resource_zabbix2->field('id,device_name')->select();
        $usergroup_arr = $Resource_zabbix_type->field('id,type_name')->select();


        $this->assign('tempresource_zabbix_type_id',$tempresource_zabbix_type_id);
        $this->assign('department_list',$department_arr);
        $this->assign('showgroupname',$showgroupname);
        $this->assign('myhostgroupoption',$myhostgroupoption);
        $this->assign('exitmyhostgroupoption',$exitmyhostgroupoption);
        $this->assign('usergroupoption',$usergroupoption);
        $this->assign('checkusergroupoption',$checkusergroupoption);
        $this->assign('tempusergroup_id',$tempusergroup_id);
   
        $this->assign('usergroup_list',$usergroup_arr);
        $this->assign('url_flag','chukou'); //left flag

        $this->display();




}





public function truncate(){

    $Week_port_config = M('week_port_config');
    $Resource_zabbix2 = M('resource_zabbix2');

    $resource_zabbix_type_id = $_REQUEST['resource_zabbix_type_id'];
    empty($resource_zabbix_type_id) && $this->error('请输入ID');

    $where['resource_zabbix_typeid'] = $resource_zabbix_type_id;

    //-------------------------------强制清空同步更新表-----------------------------------
    $Week_port_configlist=$Week_port_config->where('resource_zabbix_typeid="'.$resource_zabbix_type_id.'"')->select();
    $delids='';
    for($i=0;$i<count($Week_port_configlist);$i++){
        $resource_zabbix2id=$Week_port_configlist[$i]['resource_zabbix2id'];
        if (!empty($resource_zabbix2id)){
            $firstupdatedata['status']=0;
            $Resource_zabbix2->where('id = "'.$resource_zabbix2id.'" ')->save($firstupdatedata);
        }        
    }        
    

    //------------------------------------------------------------------



    $del = $Week_port_config->where($where)->delete();
    if ($del) {
        $this->success("删除成功");
    }else{
        $this->error("删除失败");
    }
}






public function room(){
    $Room = M('room');

    
    if(!is_login()){
      $this->redirect("User/login");
    }   
    $uid        =   is_login();
    $depart_ids=get_hostgroup_by_uid($uid);
    $depart_id=implode(",",$depart_ids);


    $room_list = $Room->order('id desc')->select();

    $this->assign("room_list",$room_list);
    $this->assign('url_flag','chukou'); //left flag
    $this->display();
}






public function add_room(){
    $Room = M('room');
    $uid        =   is_login();

    $depart_id=single_depart_id($uid);

    if ( IS_POST ) {
        
        $roomcode = $_REQUEST['roomcode'];
        $name = $_REQUEST['name'];
        // $fid = $_REQUEST['fid'];
        // $pay_bandwidth = $_REQUEST['pay_bandwidth'];

        
        $data['roomcode'] = $roomcode;
        $data['name'] = $name;
        $data['depart_id'] = $depart_id;

        $alist=$Room->data($data)->add();
        if (!empty($alist)){
            $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("ExportFlow/room")}"; </script>');
            exit();
        }
    
    }

    $this->assign('url_flag','chukou'); //left flag
    $this->display();
}





public function edit_room(){

        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);
        
        $Room = M('room');
        $id = $_REQUEST['id'];
        $room_info = $Room->where("id = $id")->find();

   
        if ( IS_POST ) {
    
            $roomcode = $_REQUEST['roomcode'];
            $name = $_REQUEST['name'];

    
            $data['roomcode'] = $roomcode;
            $data['name'] = $name;

            $alist=$Room->where("id = $id")->save($data);
            if (!empty($alist)){

                $url = U("ExportFlow/room");
                $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }else{
                $url = U("ExportFlow/room");
                $url = '<script type="text/javascript" >alert("更新失败");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }
    
        }
        $fids = $room->where("id in (".$depart_id.")")->select();
        $this->assign("list",$fids);
        $this->assign("room_info",$room_info);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }











public function del_room(){

    $Room = M('room');
    $id = $_REQUEST['id'];
    empty($id) && $this->error('请输入ID');

    $where['id'] = $id;
    $del = $Room->where($where)->delete();
    if ($del) {
        $this->success("删除成功");
    }else{
        $this->error("删除失败");
    }
}






    public function mis_type(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        $Room = M('room');
        $Mis_type = M('mis_type');
        $list = $Mis_type->where(" depart_id in (".$depart_id.")")->select();
        for($i=0;$i<count($list);$i++){
            
            $Room->where('roomcode ="'.$list[$i]['roomcode'].'"')->select;

        }
        
        $this->assign("list",$list);
        $this->assign('url_flag','chukou'); //left flag
        $this->display();
    }



















}?>
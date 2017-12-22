<?php
namespace Home\Controller;
use Think\Controller;
class  MisController extends HomeController {
    //出口带宽分布
    public function index(){
        $Mis_month = M('mis_month');
        $Room = M('room');
        $Week_product = M('week_product');
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        
        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        $list = $Mis_month->order('roomcode desc')->select();
        for($i=0;$i<count($list);$i++){
            
            $roomcode=$list[$i]['roomcode'];
            $productcode=$list[$i]['productcode'];

            $roomlist=$Room->where('roomcode = "'.$roomcode.'" ')->find();
            $list[$i]['roomname']=$roomlist['name'];
//echo 'roomcode = "'.$roomcode.'" '.$roomlist['name']."<br>";
            $product_list=$Week_product->where('productcode ="'.$productcode.'"')->find();
            $list[$i]['productname']=$product_list['name'];

        }





        $this->assign("list",$list);
        $this->assign('url_flag','Mis'); //left flag
        $this->display();
    }



    public function mis_addtype(){
        $Mis_month = M('mis_month');
        $Room = M('room');
        $Week_product = M('week_product');
        $City = M('city');
        $uid        =   is_login();

        $citylist = $City->select();
        $roomlist = $Room->select();
        $productlist = $Week_product->select();
     


        if ( IS_POST ) {
            $area = $_REQUEST['area'];
            $roomcode = $_REQUEST['roomcode'];
            $citycode = $_REQUEST['citycode'];
            $productcode = $_REQUEST['productcode'];
            $pay_bandwidth = $_REQUEST['pay_bandwidth'];
        
            empty($area) && $this->error('请选择大区');
            empty($roomcode) && $this->error('请选择机房');
            empty($citycode) && $this->error('请选择城市');
            empty($productcode) && $this->error('请选择产品');
            
            $data['area'] = $area;
            $data['roomcode'] = $roomcode;
            $data['citycode'] = $citycode;
            $data['productcode'] = $productcode;

            $product_list=$Week_product->where('productcode ="'.$productcode.'"')->find();
            $data['pname']=$product_list['name'];

            $data['pay_bandwidth'] = $pay_bandwidth;

            $checklist=$Mis_month->where('roomcode = "'.$roomcode.'" and productcode = "'.$productcode.'"')->select();
            if(empty($checklist)){
                $alist=$Mis_month->data($data)->add();
                if (!empty($alist)){
                    $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Mis/index")}"; </script>');
                    exit();
                }
            }else{
                    $this->error('重复添加！');

            }

        
        }

        $this->assign('url_flag','Mis');
        $this->assign('citylist',$citylist);
        $this->assign('productlist',$productlist);
        $this->assign('roomlist',$roomlist);

        $this->display();
    }






    
    public function mis_edittype(){

        $Mis_month = M('mis_month');
        $Room = M('room');
        $Week_product = M('week_product');
        $City = M('city');
        $uid        =   is_login();

        $citylist = $City->select();
        $roomlist = $Room->select();
        $productlist = $Week_product->select();


        $id = $_REQUEST['id'];
        empty($id) && $this->error('请选择参数');
        $Mis_month_info = $Mis_month->where("id = $id")->find();

   
        if ( IS_POST ) {
    
            $area = $_REQUEST['area'];
            $roomcode = $_REQUEST['roomcode'];
            $citycode = $_REQUEST['citycode'];
            $productcode = $_REQUEST['productcode'];
            $pay_bandwidth = $_REQUEST['pay_bandwidth'];
        
            empty($area) && $this->error('请选择大区');
            empty($roomcode) && $this->error('请选择机房');
            empty($citycode) && $this->error('请选择城市');
            empty($productcode) && $this->error('请选择产品');
    
            $data['area'] = $area;
            $data['roomcode'] = $roomcode;
            $data['citycode'] = $citycode;
            $data['productcode'] = $productcode;
            $p_list=$Week_product->where('productcode ="'.$productcode.'"')->find();
            $data['pname']=$p_list['name'];

            $data['pay_bandwidth'] = $pay_bandwidth;

            $alist=$Mis_month->where("id = $id")->save($data);
            if (!empty($alist)){
                $url = U("Mis/index");
                $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }else{
                $url = U("Mis/index");
                $url = '<script type="text/javascript" >alert("更新失败");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }
    
        }

        $this->assign("citylist",$citylist);
        $this->assign("roomlist",$roomlist);
        $this->assign("productlist",$productlist);
        $this->assign("Mis_month_info",$Mis_month_info);
        $this->assign('url_flag','Mis'); //left flag
        $this->display();
    }





public function mis_deltype(){

    $Mis_month = M('mis_month');
    $id = $_REQUEST['id'];
    empty($id) && $this->error('请输入ID');

    $where['id'] = $id;
    $del = $Mis_month->where($where)->delete();
    if ($del) {
        $this->success("删除成功");
    }else{
        $this->error("删除失败");
    }
}






    public function index2(){
        $City = M('city');
        $Room = M('room');
        $Week_product = M('week_product');
        $Mis_month = M('mis_month');
        $Resource_zabbix2 = M('resource_zabbix2');

        // $device_name = $_POST['device_name'];
        if ( IS_POST ) {


            $condition = array();
            if ($_REQUEST['device_name']) {
                    $condition['device_name'] = array('like',"%".$_REQUEST['device_name']."%");
                }
            $search_list=$Resource_zabbix2->where($condition)->select();

        }
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        
        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        $mis_month_list = $Mis_month->where('depart_id in ('.$depart_id.')')->order('roomcode desc')->select();
        for($i=0;$i<count($mis_month_list);$i++){
            $roomcode = $mis_month_list[$i]['roomcode'];
            $list = $Room->where('roomcode = "'.$roomcode.'"  ')->find();
            $mis_month_list[$i]['roomname']=$list['name'];

            $productcode = $mis_month_list[$i]['productcode'];
            $productlist = $Week_product->where('productcode = "'.$productcode.'"  ')->find();
            $mis_month_list[$i]['productname']=$productlist['name'];

            $citycode = $mis_month_list[$i]['citycode'];
            $citytlist = $City->where('citycode = "'.$citycode.'"  ')->find();
            $mis_month_list[$i]['cityname']=$citytlist['name'];


        }
        
        $this->assign("device_name",$_REQUEST['device_name']);
        $this->assign("search_list",$search_list);
        $this->assign("type_list",$mis_month_list);
        $this->assign('url_flag','Mis'); //left flag
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
        $this->assign('url_flag','Mis'); //left flag
        $this->display();
    }


    public function mis_product(){
        $Week_product = M('week_product');
     
        
        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $product_list = $Week_product->order('id desc')->select();

        $this->assign("product_list",$product_list);
        $this->assign('url_flag','Mis'); //left flag
        $this->display();
    }




    public function port_list2(){
        $Room = M('room');
        $Mis_month = M('mis_month');
        $Resource_zabbix2 = M('resource_zabbix2');
        
        $roomcode = $_REQUEST['roomcode'];
        $citycode = $_REQUEST['citycode'];
        $productcode = $_REQUEST['productcode'];

        $Mis_month_list = $Mis_month->group('roomcode')->select();
        for($i=0;$i<count($Mis_month_list);$i++){
          $temproomcode=$Mis_month_list[$i]['roomcode'];
          $rtlist = $Room->where('roomcode = "'.$temproomcode.'"  ')->find();
          $Mis_month_list[$i]['roomname']=$rtlist['name'];             
        }


        $port_list = $Resource_zabbix2->where("roomcode = '".$roomcode."' and  citycode = '".$citycode."' ")->order('id desc')->select();// and  productcode = '".$productcode."'
        //echo "roomcode = '".$roomcode."' and  citycode = '".$citycode."' ";
        $type_info = $Mis_month->where("roomcode = '".$roomcode."' ")->find();
        $depart_id = $type_info['depart_id'];        
        
        $this->assign("Mis_month_list",$Mis_month_list);
        $this->assign("depart_id",$depart_id);
        $this->assign("roomcode",$roomcode);
        $this->assign("citycode",$citycode);
        $this->assign("productcode",$productcode);
        $this->assign("port_list",$port_list);
        $this->assign('url_flag','Mis'); //left flag
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

        $this->assign('url_flag','Mis'); //left flag
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

        $this->assign('url_flag','Mis'); //left flag
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

        $this->assign('url_flag','Mis'); //left flag
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

        $this->assign('url_flag','Mis'); //left flag
        $this->display();
    }










    public function add_port2(){
        $types = M('resource_zabbix_type');
        $Room = M('room');
        $City = M('city');
        $Week_product = M('week_product');
        $ports = M('resource_zabbix2');
        $Traffic_portlist = M('traffic_portlist');
        
        $depart_id = $_REQUEST['depart_id'];
        $type_list = $types->where("depart_id = $depart_id")->select();
        
        $city_list=$City->order('id asc')->select();
        $product_list=$Week_product->order('id asc')->select();
        $room_list=$Room->order('id asc')->select();



        if ( IS_POST ) {
            $device_name = $_REQUEST['device_name'];
            $ip = $_REQUEST['ip'];
            $port = $_REQUEST['port'];
            $flag_change = $_REQUEST['flag_change'];
            $city_code = $_REQUEST['city_code'];
            $productcode = $_REQUEST['productcode'];
            $roomcode = $_REQUEST['roomcode'];

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
            empty($productcode) && $this->error('产品编码参数不能为空！');
            empty($roomcode) && $this->error('机房编码参数不能为空！');

            
            $data['roomcode'] = $roomcode;
            $data['citycode'] = $city_code;
            $data['productcode'] = $productcode;
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
                    $url = U("Mis/port_list2?type_id=".$type_id);
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
        $this->assign('url_flag','Mis'); //left flag
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
        $ports = M('resource_zabbix2');
        $Traffic_portlist = M('traffic_portlist');
        $City = M('city');
        $Week_product = M('week_product');
        $Room = M('room');

        $id = $_REQUEST['id'];
        $port_info = $ports->where("id = $id")->find();
        $depart_id = $port_info['depart_id'];
        $type_list = $types->where("depart_id = $depart_id")->select();

        $city_list=$City->order('id asc')->select();
        $product_list=$Week_product->order('id asc')->select();
        $room_list=$Room->order('id asc')->select();


        if ( IS_POST ) {
            $device_name = $_REQUEST['device_name'];
            $ip = $_REQUEST['ip'];
            $port = $_REQUEST['port'];
            $flag_change = $_REQUEST['flag_change'];
    
            $type_id = $_REQUEST['type_id'];
            $citycode = $_REQUEST['citycode'];
            $productcode = $_REQUEST['productcode'];
            $roomcode = $_REQUEST['roomcode'];
            $type_info = $types->where("id = $type_id")->find();
            $list = $Traffic_portlist->where('host = "'.$ip.'" and portname="'.$port.'" ')->select();
            $hostid=$list[0]['hostid'];
            $initemid=$list[0]['initemid'];
            $outitemid=$list[0]['outitemid'];
    
            empty($citycode) && $this->error('城市参数不能为空！');
            empty($productcode) && $this->error('产品编码参数不能为空！');
            empty($roomcode) && $this->error('机房编码参数不能为空！');

            
            $data['roomcode'] = $roomcode;
            $data['citycode'] = $citycode;
            $data['productcode'] = $productcode;

            $data['type_id'] = $type_id;
            $data['port'] = $port;
            $data['ip'] = $ip;
            $device_name_list = explode(" ", $device_name);
            $data['device_name'] = $device_name_list[0].' '.$port;
            $data['ip'] = $ip;
            //$data['hostid'] = $hostid;
            //$data['initemid'] = $initemid;
            //$data['outitemid'] = $outitemid;
            $data['depart_id'] = $depart_id;
            $data['flag_change'] = $flag_change;
            $data['flag_type'] = $type_info["type_name"];
            $data['type_name'] = $type_info["type_name"];

    
            //$checklist=$ports->where('port = "'.$port.'" and ip = "'.$ip.'" and device_name = "'.$device_name.'"')->select();
            $list=$ports->where("id = $id")->save($data);
            if (!empty($list)){
                    $url = U("Mis/port_list2?type_id=".$type_id);
                    $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
                    echo $url;
                    exit();
            }else{
                $this->error("更新失败");exit();
            }
    
    
        }
        $this->assign("room_list",$room_list);
        $this->assign("product_list",$product_list);
        $this->assign("city_list",$city_list);
        $this->assign("port_info",$port_info);
        $this->assign("type_list",$type_list);
        $this->assign('depart_id',$depart_id);
        $this->assign('url_flag','Mis'); //left flag
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
    


     public function add_city(){
        $City = M('city');
        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);

        if ( IS_POST ) {
            
            $citycode = $_REQUEST['citycode'];
            $name = $_REQUEST['name'];
            
            $data['citycode'] = $citycode;
            $data['name'] = $name;

            $checklist=$City->where('citycode = "'.$citycode.'" ')->select();
            if(empty($checklist)){
                $alist=$City->data($data)->add();
                if (!empty($alist)){
                    $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Mis/city")}"; </script>');
                    exit();
                }
            }else{
                    $this->error('重复添加！');
            }
        
        }

        $this->assign('url_flag','Mis'); //left flag
        $this->display();
    }


    public function mis_add_product(){
        $Week_product = M('week_product');
        $uid        =   is_login();

        if ( IS_POST ) {
            $name = $_REQUEST['name'];
            $productcode = $_REQUEST['productcode'];
            $roomcode = $_REQUEST['roomcode'];
            $citycode = $_REQUEST['citycode'];
            $area = $_REQUEST['area'];
            
            // empty($area) && $this->error('大区参数不能为空！');
            // empty($citycode) && $this->error('城市参数不能为空！');
            // empty($roomcode) && $this->error('机房参数不能为空！');


            $data['name'] = $name;
            //$data['citycode'] = $citycode;
            //$data['roomcode'] = $roomcode;
            //$data['area'] = $area;
            $data['productcode'] = $productcode;
           
            $checklist=$Week_product->where('productcode = "'.$productcode.'" ')->select();
            if(empty($checklist)){
                $alist=$Week_product->data($data)->add();
                if (!empty($alist)){
                    $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Mis/mis_product")}"; </script>');
                    exit();
                }
            }else{
                    $this->error('重复添加！');
            }

        }
        $this->assign('url_flag','Mis'); //left flag
        $this->display();
    }
    




public function mis_edit_product(){

        $uid        =   is_login();
        $Week_product = M('week_product');

        $id = $_REQUEST['id'];
        $week_product_info = $Week_product->where("id = $id")->find();

   
        if ( IS_POST ) {
            $id = $_REQUEST['id'];
            $name = $_REQUEST['name'];
            $productcode = $_REQUEST['productcode'];

            $data['name'] = $name;
            $data['productcode'] = $productcode;

            $alist=$Week_product->where("id = $id")->save($data);
            if (!empty($alist)){
                $url = U("Mis/mis_product");
                $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }else{
                $url = U("Mis/mis_product");
                $url = '<script type="text/javascript" >alert("更新失败");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }
    
        }

        $this->assign("week_product_info",$week_product_info);
        $this->assign('url_flag','Mis'); //left flag
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
        $this->assign('url_flag','Mis'); //left flag
        $this->display();
    }
    
public function edit_city(){

        $uid        =   is_login();

        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);
        
        $City = M('city');
        $id = $_REQUEST['id'];
        $city_info = $City->where("id = $id")->find();

   
        if ( IS_POST ) {
    
            $id = $_REQUEST['id'];
            $citycode = $_REQUEST['citycode'];
            $name = $_REQUEST['name'];

            $data['citycode'] = $citycode;
            $data['name'] = $name;

            $alist=$City->where("id = $id")->save($data);
            if (!empty($alist)){
                $url = U("Mis/city");
                $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }else{
                $url = U("Mis/city");
                $url = '<script type="text/javascript" >alert("更新失败");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }
    
        }

        $this->assign("city_info",$city_info);
        $this->assign('url_flag','Mis'); //left flag
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


     public function mis_del_product(){
    
        $Week_product = M('week_product');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $Week_product->where($where)->delete();
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
        $this->assign('url_flag','Mis'); //left flag
        $this->display();

    }





    public function mis_history(){

        $Mis_history = M('mis_history');
        $Room = M('room');
        $Week_product = M('week_product');


        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        
        $depart_ids=get_hostgroup_by_uid($uid);
        $depart_id=implode(",",$depart_ids);


        $start = $_REQUEST['start'];        
        $end = $_REQUEST['end'];
        $report_type = $_REQUEST['report_type'];
        $date=explode("-",$start);
        $year=$date[0];
        $month=$date[1];

        if ( IS_POST ) {
            $report_type = $_REQUEST['report_type'];

            if(empty($start) && empty($end)){$this->error('请输入正确的时间周期!');}

            if(empty($report_type)){
                $list = $Mis_history->where("depart_id in (".$depart_id.") and DATE_FORMAT(day,'%Y-%m-%d')>='".$start."' and DATE_FORMAT(day,'%Y-%m-%d')<='".$end."' ")->field('avg(bandwidth_month) as avgbandwidth_month,avg(bandwidth_day) as avgbandwidth_day,avg(bandwidth_week) as avgbandwidth_week,avg(bandwidth_doubleweek) as avgbandwidth_doubleweek,citycode,roomcode,productcode,bandwidth_month,year,month,bandwidth_day,day,bandwidth_week,bandwidth_doubleweek,dweek_etime,dweek_stime,id,week_etime,week_stime')->order('roomcode desc')->select();
           //echo "depart_id in (".$depart_id.") and day>='".$start."' and day<='".$end."' ";
            }else{
                if($report_type=='mis_day'){
                    $where="report_type ='mis_day' and depart_id in (".$depart_id.") and DATE_FORMAT(day,'%Y-%m-%d')='".$start."'  ";
                }elseif($report_type=='mis_week'){  //FROM_UNIXTIME(week_stime, '%Y-%m-%d') 
                    $where="report_type ='mis_week' and depart_id in (".$depart_id.") and FROM_UNIXTIME(week_stime,'%Y-%m-%d')='".$start."'   ";                    
                }elseif($report_type=='mis_doubleweek'){
                    $where="report_type ='mis_doubleweek' and depart_id in (".$depart_id.") and FROM_UNIXTIME(dweek_stime,'%Y-%m-%d')='".$start."'  ";                                        
                }elseif($report_type=='mis_month'){
                    $where="report_type ='mis_month' and depart_id in (".$depart_id.") and year='".$year."' and  month='".$month."'  ";                                                            
                }else{
                    $where="report_type ='mis_month' and depart_id in (".$depart_id.") and year='".$year."' and  month='".$month."'  ";                                                                               
                }
                //echo $where;
                $list = $Mis_history->where($where)->field('avg(bandwidth_month) as avgbandwidth_month,avg(bandwidth_day) as avgbandwidth_day,avg(bandwidth_week) as avgbandwidth_week,avg(bandwidth_doubleweek) as avgbandwidth_doubleweek,citycode,roomcode,productcode,bandwidth_month,year,month,bandwidth_day,day,bandwidth_week,bandwidth_doubleweek,dweek_etime,dweek_stime,id,week_etime,week_stime')->order('roomcode desc')->GROUP('name')->select();               
                
            }

            for($i=0;$i<count($list);$i++){
                
                $roomcode=$list[$i]['roomcode'];
                $productcode=$list[$i]['productcode'];

                $roomlist=$Room->where('roomcode = "'.$roomcode.'" ')->find();
                $list[$i]['roomname']=$roomlist['name'];
    //echo 'roomcode = "'.$roomcode.'" '.$roomlist['name']."<br>";
                $product_list=$Week_product->where('productcode ="'.$productcode.'"')->find();
                $list[$i]['productname']=$product_list['name'];
                
                $list[$i]['avgbandwidth_month'] = round($list[$i]['avgbandwidth_month'],2);
                $list[$i]['avgbandwidth_day'] = round($list[$i]['avgbandwidth_day'],2);
                $list[$i]['avgbandwidth_doubleweek'] = round($list[$i]['avgbandwidth_doubleweek'],2);
                $list[$i]['avgbandwidth_week'] = round($list[$i]['avgbandwidth_week'],2);

            }
        }

        $this->assign("end",$_REQUEST['end']);
        $this->assign("start",$_REQUEST['start']);
        $this->assign("report_type",$_REQUEST['report_type']);
        $this->assign("list",$list);
        $this->assign('url_flag','Mis'); //left flag
        $this->display();

    }






/**出口端口管理**/
public function mis_portmanger() {

    $uid        =   is_login();
    
    $depart_ids=get_hostgroup_by_uid($uid);
    $depart_id=implode(",",$depart_ids);

    $Resource_zabbix2 = M('resource_zabbix2');
    $Room = M('room');
    $Mis_month = M('mis_month');
    $Week_product = M('week_product');
    $Mis_port_config = M('mis_port_config');



    $tempresource_zabbix2_id=$_REQUEST['resource_zabbix2_id'];
    $tempmis_month_id=$_REQUEST['mis_month_id'];
    $selfhostgroup_id=$_REQUEST['selfhostgroup_id'];


    $roomlist=$Mis_month->where('depart_id in ('.$depart_id.')')->group('roomcode')->select();

    for($i=0;$i<count($roomlist);$i++){
        $temproomcode=$roomlist[$i]['roomcode'];
        $realroomlist=$Room->where('roomcode="'.$temproomcode.'"')->find();
        $roomname=$realroomlist['name'];
        $roomlist[$i]['roomname']=$roomname;
    }



    $checkusergroupoption="";
    $usergrouplist=$Mis_month->where('depart_id in ('.$depart_id.')')->order(' id desc')->select();
    for($i=0;$i<count($usergrouplist);$i++){
       $id=$usergrouplist[$i]['id'];
       $temproomcode=$usergrouplist[$i]['roomcode'];
       $productcode=$usergrouplist[$i]['productcode'];
       $plist=$Week_product->where('productcode="'.$productcode.'"')->find();
       $productname=$plist['name'];
       $usergrouplist[$i]['pname']=$productname;
       if ($tempmis_month_id==$id){
            $usergroupoption.='<option value="'.$id.'" selected="selected">'.$productname.'</option>';
            $checkusergroupoption.='<option value="'.__ROOT__.'/index.php?s=/Home/Mis/mis_portmanger/roomcode/'.$temproomcode.'/mis_month_id/'.$id.'.html" selected="selected">'.$productname.'</option>';
       }else{
            $usergroupoption.='<option value="'.$id.'">'.$productname.'</option>';
            $checkusergroupoption.='<option value="'.__ROOT__.'/index.php?s=/Home/Mis/mis_portmanger/roomcode/'.$temproomcode.'/mis_month_id/'.$id.'.html">'.$productname.'</option>';
       }
       
    }


    if (!empty($tempmis_month_id)){


      //已经拥有系统访问权限

          $showgrouplist=$Mis_port_config->where(' mis_month_id ="'.$tempmis_month_id.'" ')->select();
          $showallid='';
          for($j=0;$j<count($showgrouplist);$j++){
              $showhostgroup_id=$showgrouplist[$j]['resource_zabbix2id'];
              $showallid=$showallid.','.$showhostgroup_id;
          }
          $showallid=substr($showallid,1,strlen($showallid));

          $myhostgroupoption="";

      if (!empty($showallid)){

        
          $myhostgrouplist=$Resource_zabbix2->where(' id in ('.$showallid.') ')->select();
          //echo ' id  in ('.$showallid.') ';
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
          $exitmyhostgrouplist=$Resource_zabbix2->where(' id not in ('.$showallid.') ')->select();//and depart_id in ('.$depart_id.')
          //echo ' id not in ('.$showallid.') ';
          for($i=0;$i<count($exitmyhostgrouplist);$i++){
             $id=$exitmyhostgrouplist[$i]['id'];
             $device_name=$exitmyhostgrouplist[$i]['device_name'];
             if ($tempmis_month_id==$id){
                  $exitmyhostgroupoption.='<option value="'.$id.'" selected="selected">'.$device_name.'</option>';
             }else{
                  $exitmyhostgroupoption.='<option value="'.$id.'">'.$device_name.'</option>';
             }
          }
         
      }else{

          $exitmyhostgroupoption="";
          $exitmyhostgrouplist=$Resource_zabbix2->select();//->where('depart_id in ('.$depart_id.')')
//var_dump($exitmyhostgrouplist);
          for($i=0;$i<count($exitmyhostgrouplist);$i++){
             $id=$exitmyhostgrouplist[$i]['id'];
             $device_name=$exitmyhostgrouplist[$i]['device_name'];
             if ($tempmis_month_id==$id){
                  $exitmyhostgroupoption.='<option value="'.$id.'" selected="selected">'.$device_name.'</option>';
             }else{
                  $exitmyhostgroupoption.='<option value="'.$id.'">'.$device_name.'</option>';
             }
          }


      }

  } 





    if(IS_POST){

        $tempresource_zabbix2_id=$_REQUEST['resource_zabbix2_id'];
        $tempmis_month_id=$_REQUEST['mis_month_id'];
        $selfhostgroup_id=$_REQUEST['selfhostgroup_id'];
        $roomcode=$_REQUEST['roomcode'];

        if(empty($tempmis_month_id)){$this->error('产品类型不能为空!');}     
        if(empty($selfhostgroup_id)){$this->error('端口不能为空!');}

        for($i=0;$i<count($selfhostgroup_id);$i++){
            $dataList[]=array('resource_zabbix2id'=>$selfhostgroup_id[$i],'mis_month_id'=>$tempmis_month_id);
        }

        $mis_port_configlist=$Mis_port_config->where('mis_month_id="'.$tempmis_month_id.'"')->select();
        for($i=0;$i<count($mis_port_configlist);$i++){
            $resource_zabbix2id=$mis_port_configlist[$i]['resource_zabbix2id'];
            if (!empty($resource_zabbix2id)){
                $firstupdatedata['misstatus']=0;
                $Resource_zabbix2->where('id ="'.$resource_zabbix2id.'" ')->save($firstupdatedata);
            }            
        }        
        
        //删除
        $Mis_port_config->where('mis_month_id="'.$tempmis_month_id.'"')->delete();
        if($Mis_port_config->addAll($dataList)){
          
          for($i=0;$i<count($selfhostgroup_id);$i++){
            $updatedata['misstatus']=1;
            $Resource_zabbix2->where('id ="'.$selfhostgroup_id[$i].'"')->save($updatedata);

          }

          //$url = U("ExportFlow/portmanger?resource_zabbix_type_id=".$tempresource_zabbix_type_id);
          //$url = '<script type="text/javascript" >alert("配置操作成功");window.location.href="'.$url.'"; </script>';
          $this->show('<script type="text/javascript" >alert("配置操作成功!");window.location.href="'.__ROOT__.'/index.php?s=/Home/Mis/mis_portmanger/mis_month_id/'.$tempmis_month_id.'.html"; </script>');
          exit();
        }else{
          $this->error('配置操作有误');
        }

    }







        $department_arr = $Resource_zabbix2->field('id,device_name')->select();
        $usergroup_arr = $Mis_month->field('id,productcode')->select();

        $this->assign('roomlist',$roomlist);
        $this->assign('tempmis_month_id',$tempmis_month_id);
        $this->assign('department_list',$department_arr);
        $this->assign('showgroupname',$showgroupname);
        $this->assign('myhostgroupoption',$myhostgroupoption);
        $this->assign('exitmyhostgroupoption',$exitmyhostgroupoption);
        $this->assign('usergroupoption',$usergroupoption);
        $this->assign('checkusergroupoption',$checkusergroupoption);
        $this->assign('tempusergroup_id',$tempusergroup_id);
   
        $this->assign('usergroup_list',$usergroup_arr);
        $this->assign('url_flag','Mis'); //left flag
        $this->assign('jsonlist2',json_encode($usergrouplist));

        $this->display();




}






public function mis_truncate(){

    $Mis_port_config = M('mis_port_config');
    $Resource_zabbix2 = M('resource_zabbix2');

    $mis_month_id = $_REQUEST['mis_month_id'];
    empty($mis_month_id) && $this->error('请输入ID');

    $where['mis_month_id'] = $mis_month_id;

    //-------------------------------强制清空同步更新表-----------------------------------
    $Mis_port_configlist=$Mis_port_config->where('mis_month_id="'.$mis_month_id.'"')->select();
    $delids='';
    for($i=0;$i<count($Mis_port_configlist);$i++){
        $resource_zabbix2id=$Mis_port_configlist[$i]['resource_zabbix2id'];
        if (!empty($resource_zabbix2id)){
            $firstupdatedata['status']=0;
            $Resource_zabbix2->where('id = "'.$resource_zabbix2id.'" ')->save($firstupdatedata);
        }        
    }        
    

    //------------------------------------------------------------------



    $del = $Mis_port_config->where($where)->delete();
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
    $this->assign('url_flag','Mis'); //left flag
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
            $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Mis/room")}"; </script>');
            exit();
        }
    
    }

    $this->assign('url_flag','Mis'); //left flag
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

                $url = U("Mis/room");
                $url = '<script type="text/javascript" >alert("更新成功");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }else{
                $url = U("Mis/room");
                $url = '<script type="text/javascript" >alert("更新失败");window.location.href="'.$url.'"; </script>';
                echo $url;
                exit();
            }
    
        }

        $this->assign("room_info",$room_info);
        $this->assign('url_flag','Mis'); //left flag
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


























}?>
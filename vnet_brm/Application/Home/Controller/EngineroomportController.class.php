<?php
namespace Home\Controller;
use Think\Controller;
class  EngineroomportController extends HomeController {
    
    public function index(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $Jifang_flow = M('jifang_flow');
        $Department = M('department');
        
        $jifangid = $_REQUEST['jifangid'];
        $depart_list = $Department->select();
        $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
        
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $condition = array();
        if (!IS_ROOT) {
            //$condition['depart_id'] = array('in',$arr);
        }

        if ($_GET['jifangid']) {
            $condition['id'] = $_GET['jifangid'];
        }

        $option='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/index.html"   >请选择机房......</option>';
        $Jifang_flow_list=$Jifang_flow->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_flow_list);$i++){
           $flowid=$Jifang_flow_list[$i]['id'];
           $name=$Jifang_flow_list[$i]['name'];
           if ($jifangid==$flowid){
               $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/index/jifangid/'.$flowid.'.html" selected="selected" >'.$name.'</option>';
           }else{
               $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/index/jifangid/'.$flowid.'.html" >'.$name.'</option>';
           }
           

        }



        $list = $Jifang_flow->where($condition)->order('id desc')->select();
        


        $this->assign('option',$option);
        $this->assign('depart_arr_now',$depart_arr_now);
        $this->assign('list',$list);
        $this->assign('url_flag','Engineroomport'); //left flag
        $this->display();
    }



    public function add_port(){
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Jifang_flow = M('jifang_flow');
        $name = $_REQUEST['name'];
        $jifang_url_id = $_REQUEST['jifangid'];

        if ( IS_POST ) {
            
            $data['name'] = $_REQUEST['name'];
            $checklist=$Jifang_flow->where('name = "'.$name.'" ')->select();
            if (empty($checklist)){
                $list=$Jifang_flow->data($data)->add();
                if (!empty($list)){
                    $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Engineroomport/index")}"; </script>');
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("重复添加，请重新添加");window.location.href="{:U("Engineroomport/add_port")}"; </script>');
            }


        }

        $this->assign('jifang_url_id',$jifang_url_id); //left flag
        $this->assign('url_flag','Engineroomport'); //left flag
        $this->display();
    }
    
    public function edit_port(){
        //echo Sec2Time(10000);die;
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Jifang_flow = M('jifang_flow');
        
        $id = $_REQUEST['id'];
        $Jifang_flow_find = $Jifang_flow->where("id = $id")->find();

        if ( IS_POST ) {

            $data['name'] = $_REQUEST['name'];
    
            $list=$Jifang_flow->where("id = $id")->setField($data);
            if (!empty($list)){
                $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Engineroomport/index")}"; </script>');
                exit();
            }
        }
        
        $this->assign("id",$id);
        $this->assign("Jifang_flow_find",$Jifang_flow_find);
        $this->assign('url_flag','Engineroomport'); //left flag
        $this->display();
    }
    

    
    public function del_port(){
    
        $Jifang_flow = M('jifang_flow');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $Jifang_flow->where($where)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Engineroomport/index")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Engineroomport/index")}"; </script>');die;
        }
    }
    
    



    public function portlist(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $Jifang_flow = M('Jifang_flow');
        $Jingfang_param = M('jingfang_param');
        
        $jifangid = $_REQUEST['jifangid'];
        
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $condition = array();
        if (!IS_ROOT) {
            //$condition['depart_id'] = array('in',$arr);
        }

        if ($_GET['jifangid']) {
            $condition['jifang_id'] = $_GET['jifangid'];
        }

        $option='<option value="" selected="selected"  >请选择机房......</option>';
        $Jifang_flow_list=$Jifang_flow->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_flow_list);$i++){
           $flowid=$Jifang_flow_list[$i]['id'];
           $name=$Jifang_flow_list[$i]['name'];

           if ($jifangid==$flowid){
               $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist/jifangid/'.$flowid.'.html" selected="selected" >'.$name.'</option>';
           }else{
               $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist/jifangid/'.$flowid.'.html" >'.$name.'</option>';
           }
           

        }

        $list = $Jingfang_param->where($condition)->order('id desc')->select();
        
        $this->assign('jifang_url_id',$jifangid);
        $this->assign('option',$option);
        $this->assign('depart_arr_now',$depart_arr_now);
        $this->assign('list',$list);
        $this->assign('url_flag','Engineroomport'); //left flag
        $this->display();
    }



    public function add_param(){
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Jifang_flow = M('jifang_flow');
        $Traffic_portlist = M('traffic_portlist');
        $Jingfang_param = M('jingfang_param');
        $Ipdb_items = M('ipdb_items');
        $name = $_REQUEST['name'];
        $jifang_url_id = $_REQUEST['jifang_url_id'];


        $option='<option value="" >请选择机房......</option>';
        $Jifang_flow_list=$Jifang_flow->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_flow_list);$i++){
           $flowid=$Jifang_flow_list[$i]['id'];
           $name=$Jifang_flow_list[$i]['name'];

           if ($jifang_url_id==$flowid){
               $option.='<option value="'.$flowid.'" selected="selected">'.$name.'</option>';
            }else{
               $option.='<option value="'.$flowid.'" >'.$name.'</option>';
            }

        }

        if ( IS_POST ) {

            empty($_REQUEST['jifang_id']) && $this->error('机房参数不能为空');


            $jifang_id=$_REQUEST['jifang_id'];
            $ip=$_REQUEST['ip'];
            $port=$_REQUEST['port'];
            $device_name=$_REQUEST['device_name'];
            $port_type=$_REQUEST['port_type'];
            
            $listone=$Ipdb_items->where('ipv4 = "'.$ip.'" ')->select();
            $ipv4=$listone[0]['ipv4'];
            empty($ipv4) && $this->error('设备在本系统中不存在！');


            $list=$Traffic_portlist->where('host = "'.$ip.'" and portname="'.$port.'" ')->select();
            $hostid=$list[0]['hostid'];
            $initemid=$list[0]['initemid'];
            $outitemid=$list[0]['outitemid'];

            empty($hostid) && $this->error('没加监控！');
            empty($initemid) && $this->error('没有授权或轮询为获取参数！');
            empty($outitemid) && $this->error('没有授权或轮询为获取参数！');

            $data['hostid'] = $hostid;
            $data['initemid'] = $initemid;
            $data['outitemid'] = $outitemid;

            $data['jifang_id'] = $_REQUEST['jifang_id'];
            $data['port'] = $_REQUEST['port'];
            $data['port_type'] = $_REQUEST['port_type'];
            $data['device_name'] = $_REQUEST['device_name'];
            $data['ip'] = $_REQUEST['ip'];
            $data['desc'] = $_REQUEST['desc'];



            $checklist=$Jingfang_param->where('jifang_id = "'.$jifang_id.'" and port = "'.$port.'" and ip = "'.$ip.'" and device_name = "'.$device_name.'" and port_type = "'.$port_type.'" ')->select();
            if (empty($checklist)){
                $list=$Jingfang_param->data($data)->add();
                if (!empty($list)){
                    if (empty($jifang_url_id)){
                        $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Engineroomport/index")}"; </script>');
                    }else{
                        $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist/jifangid/'.$jifang_url_id.'.html"; </script>');                        
                    }
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("重复添加，请重新添加");window.location.href="{:U("Engineroomport/add_param")}"; </script>');
            }


        }

        $this->assign("jifang_url_id",$jifang_url_id);
        $this->assign("option",$option);
        $this->assign('url_flag','Engineroomport'); //left flag
        $this->display();
    }



    public function edit_param(){
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Jifang_flow = M('jifang_flow');
        $Traffic_portlist = M('traffic_portlist');
        $Jingfang_param = M('jingfang_param');
        $name = $_REQUEST['name'];
        $jifang_id = $_REQUEST['jifang_id'];

        $id = $_REQUEST['id'];
        empty($id) && $this->error('ID参数为空!');
        $param_find = $Jingfang_param->where("id = $id")->find();

        //$Jifang_flow_l=$Jifang_flow->where('id="'.$jifang_id.'"')->select();
        //$jfname=$Jifang_flow_l[0]['name'];


        //$option='<option value="" >请选择机房......</option>';
        $Jifang_flow_list=$Jifang_flow->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_flow_list);$i++){
           $flowid=$Jifang_flow_list[$i]['id'];
           $name=$Jifang_flow_list[$i]['name'];

           if ($jifang_id==$flowid){
               $option.='<option value="'.$flowid.'" selected="selected">'.$name.'</option>';
            }else{
               //$option.='<option value="'.$flowid.'" >'.$name.'</option>';
            }

        }

        if ( IS_POST ) {

            empty($_REQUEST['jifang_id']) && $this->error('机房参数不能为空');


            $jifang_id=$_REQUEST['jifang_id'];
            $ip=$_REQUEST['ip'];
            $port=$_REQUEST['port'];
            $device_name=$_REQUEST['device_name'];
            $port_type=$_REQUEST['port_type'];

            $data['jifang_id'] = $_REQUEST['jifang_id'];
            $data['port'] = $_REQUEST['port'];
            $data['port_type'] = $_REQUEST['port_type'];
            $data['device_name'] = $_REQUEST['device_name'];
            $data['ip'] = $_REQUEST['ip'];
            $data['desc'] = $_REQUEST['desc'];

            $checklist=$Jingfang_param->where('jifang_id = "'.$jifang_id.'" and port = "'.$port.'" and ip = "'.$ip.'" and device_name = "'.$device_name.'" and port_type = "'.$port_type.'" ')->select();
            if (empty($checklist)){
                //$list=$Jingfang_param->data($data)->add();
                $list=$Jingfang_param->where("id = $id")->setField($data);
                if (!empty($list)){
                    if (empty($jifang_id)){
                        $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Engineroomport/index")}"; </script>');
                    }else{
                        $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist/jifangid/'.$jifang_id.'.html"; </script>');                        
                    }
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("重复操作，请重新编辑");window.location.href="{:U("Engineroomport/edit_param")}"; </script>');
            }


        }

        //$this->assign("jfname",$jfname);
        $this->assign("param_find",$param_find);
        $this->assign("jifang_url_id",$jifang_url_id);
        $this->assign("option",$option);
        $this->assign('url_flag','Engineroomport'); //left flag
        $this->display();
    }





    public function del_param(){
    
        $Jingfang_param = M('jingfang_param');
        $id = $_REQUEST['id'];
        $jifang_id = $_REQUEST['jifang_id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $Jingfang_param->where($where)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist/jifangid/'.$jifang_id.'.html"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist/jifangid/'.$jifang_id.'.html"; </script>');die;
        }
    }










    public function indexhuanan(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $Huanan_jifang_flow = M('huanan_jifang_flow');
        $Department = M('department');
        
        $jifangid = $_REQUEST['jifangid'];
        $depart_list = $Department->select();
        $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
        
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $condition = array();
        if (!IS_ROOT) {
            //$condition['depart_id'] = array('in',$arr);
        }

        if ($_GET['jifangid']) {
            $condition['id'] = $_GET['jifangid'];
        }

        $option='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/indexhuanan.html"   >请选择机房......</option>';
        $Jifang_flow_list=$Huanan_jifang_flow->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_flow_list);$i++){
           $flowid=$Jifang_flow_list[$i]['id'];
           $name=$Jifang_flow_list[$i]['name'];
           if ($jifangid==$flowid){
               $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/indexhuanan/jifangid/'.$flowid.'.html" selected="selected" >'.$name.'</option>';
           }else{
               $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/indexhuanan/jifangid/'.$flowid.'.html" >'.$name.'</option>';
           }
           

        }

        $list = $Huanan_jifang_flow->where($condition)->order('id desc')->select();
        
        $this->assign('option',$option);
        $this->assign('depart_arr_now',$depart_arr_now);
        $this->assign('list',$list);
        $this->assign('url_flag','huanan'); //left flag
        $this->display();
    }



    public function add_port_huanan(){
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Huanan_jifang_flow = M('huanan_jifang_flow');
        $name = $_REQUEST['name'];
        $jifang_url_id = $_REQUEST['jifangid'];

        if ( IS_POST ) {
            
            $data['name'] = $_REQUEST['name'];
            $checklist=$Huanan_jifang_flow->where('name = "'.$name.'" ')->select();
            if (empty($checklist)){
                $list=$Huanan_jifang_flow->data($data)->add();
                if (!empty($list)){
                    $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Engineroomport/indexhuanan")}"; </script>');
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("重复添加，请重新添加");window.location.href="{:U("Engineroomport/add_port_huanan")}"; </script>');
            }

        }

        $this->assign('jifang_url_id',$jifang_url_id); //left flag
        $this->assign('url_flag','huanan'); //left flag
        $this->display();
    }
    
    public function edit_port_huanan(){
        //echo Sec2Time(10000);die;
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Huanan_jifang_flow = M('huanan_jifang_flow');
        
        $id = $_REQUEST['id'];
        $Jifang_flow_find = $Huanan_jifang_flow->where("id = $id")->find();

        if ( IS_POST ) {

            $data['name'] = $_REQUEST['name'];
    
            $list=$Huanan_jifang_flow->where("id = $id")->setField($data);
            if (!empty($list)){
                $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Engineroomport/indexhuanan")}"; </script>');
                exit();
            }
        }
        
        $this->assign("id",$id);
        $this->assign("Jifang_flow_find",$Jifang_flow_find);
        $this->assign('url_flag','huanan'); //left flag
        $this->display();
    }
    

    
    public function del_port_huanan(){
    
        $Huanan_jifang_flow = M('huanan_jifang_flow');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $Huanan_jifang_flow->where($where)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Engineroomport/indexhuanan")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Engineroomport/indexhuanan")}"; </script>');die;
        }
    }



    public function portlist_huanan(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $Huanan_jifang_flow = M('huanan_jifang_flow');
        $Huanan_jifang_param = M('huanan_jifang_param');
        
        $jifangid = $_REQUEST['jifangid'];
        
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $condition = array();
        if (!IS_ROOT) {
            //$condition['depart_id'] = array('in',$arr);
        }

        if ($_GET['jifangid']) {
            $condition['jifang_id'] = $_GET['jifangid'];
        }

        $option='<option value="" selected="selected"  >请选择机房......</option>';
        $Jifang_flow_list=$Huanan_jifang_flow->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_flow_list);$i++){
           $flowid=$Jifang_flow_list[$i]['id'];
           $name=$Jifang_flow_list[$i]['name'];

           if ($jifangid==$flowid){
               $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist_huanan/jifangid/'.$flowid.'.html" selected="selected" >'.$name.'</option>';
           }else{
               $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist_huanan/jifangid/'.$flowid.'.html" >'.$name.'</option>';
           }
           

        }

        $list = $Huanan_jifang_param->where($condition)->order('id desc')->select();
        
        $this->assign('jifang_url_id',$jifangid);
        $this->assign('option',$option);
        $this->assign('depart_arr_now',$depart_arr_now);
        $this->assign('list',$list);
        $this->assign('url_flag','huanan'); //left flag
        $this->display();
    }



    public function add_huanan_param(){
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Traffic_portlist = M('traffic_portlist');
        $Huanan_jifang_flow = M('huanan_jifang_flow');
        $Huanan_jifang_param = M('huanan_jifang_param');
        $Jifang_port_type = M('jifang_port_type');
        $Ipdb_items = M('ipdb_items');

        $name = $_REQUEST['name'];
        $type_id = $_REQUEST['type_id'];
        $jifang_url_id = $_REQUEST['jifang_url_id'];


        $option='<option value="" >请选择机房......</option>';
        $Jifang_flow_list=$Huanan_jifang_flow->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_flow_list);$i++){
           $flowid=$Jifang_flow_list[$i]['id'];
           $name=$Jifang_flow_list[$i]['name'];

           if ($jifang_url_id==$flowid){
               $option.='<option value="'.$flowid.'" selected="selected">'.$name.'</option>';
            }else{
               $option.='<option value="'.$flowid.'" >'.$name.'</option>';
            }

        }

        $portoption='';
        $Jifang_port_type_list=$Jifang_port_type->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_port_type_list);$i++){
           $tempid=$Jifang_port_type_list[$i]['id'];
           $tempname=$Jifang_port_type_list[$i]['name'];

           if ($type_id==$tempid){
               $portoption.='<option value="'.$tempid.'" selected="selected">'.$tempname.'</option>';
            }else{
               $portoption.='<option value="'.$tempid.'" >'.$tempname.'</option>';
            }

        }




        if ( IS_POST ) {

            empty($_REQUEST['jifang_id']) && $this->error('机房参数不能为空');


            $jifang_id=$_REQUEST['jifang_id'];
            $ip=$_REQUEST['ip'];
            $port=$_REQUEST['port'];
            $device_name=$_REQUEST['device_name'];
            $port_type=$_REQUEST['port_type'];
            
            $listone=$Ipdb_items->where('ipv4 = "'.$ip.'" ')->select();
            $ipv4=$listone[0]['ipv4'];
            $hostid=$listone[0]['hostid'];
            empty($ipv4) && $this->error('设备在本系统中不存在！');
            empty($hostid) && $this->error('没加监控！');


            $list=$Traffic_portlist->where('host = "'.$ip.'" and portname="'.$port.'" ')->select();
            $port_hostid=$list[0]['hostid'];
            $initemid=$list[0]['initemid'];
            $outitemid=$list[0]['outitemid'];

            empty($port_hostid) && $this->error('端口不存在！');
            empty($initemid) && $this->error('没有授权或轮询为获取参数！');
            empty($outitemid) && $this->error('没有授权或轮询为获取参数！');

            $data['hostid'] = $hostid;
            $data['initemid'] = $initemid;
            $data['outitemid'] = $outitemid;

            $data['jifang_id'] = $_REQUEST['jifang_id'];
            $data['port'] = $_REQUEST['port'];
            $type_id=$_REQUEST['type_id'];
            $data['port_type_id'] = $type_id;
            $templist=$Jifang_port_type->where('id = "'.$type_id.'" ')->select();
            $port_typename=$templist[0]['name'];           
            $data['port_type'] = $port_typename;

            $data['device_name'] = $_REQUEST['device_name'];
            $data['ip'] = $_REQUEST['ip'];
            $data['desc'] = $_REQUEST['desc'];



            $checklist=$Huanan_jifang_param->where('jifang_id = "'.$jifang_id.'" and port = "'.$port.'" and ip = "'.$ip.'" and device_name = "'.$device_name.'" and port_type = "'.$port_type.'" ')->select();
            if (empty($checklist)){
                $list=$Huanan_jifang_param->data($data)->add();
                if (!empty($list)){
                    if (empty($jifang_url_id)){
                        $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Engineroomport/indexhuanan")}"; </script>');
                    }else{
                        $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist_huanan/jifangid/'.$jifang_url_id.'.html"; </script>');                        
                    }
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("重复添加，请重新添加");window.location.href="{:U("Engineroomport/add_huanan_param")}"; </script>');
            }


        }

        $this->assign("jifang_url_id",$jifang_url_id);
        $this->assign("option",$option);
        $this->assign("portoption",$portoption);
        $this->assign('url_flag','huanan'); //left flag
        $this->display();
    }



    public function edit_huanan_param(){
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Traffic_portlist = M('traffic_portlist');
        $Huanan_jifang_flow = M('huanan_jifang_flow');
        $Huanan_jifang_param = M('huanan_jifang_param');
        $Jifang_port_type = M('jifang_port_type');
        $name = $_REQUEST['name'];
        $jifang_id = $_REQUEST['jifang_id'];

        $id = $_REQUEST['id'];
        empty($id) && $this->error('ID参数为空!');
        $param_find = $Huanan_jifang_param->where("id = $id")->find();
        $type_id=$param_find['port_type_id'];
        //$Jifang_flow_l=$Jifang_flow->where('id="'.$jifang_id.'"')->select();
        //$jfname=$Jifang_flow_l[0]['name'];


        //$option='<option value="" >请选择机房......</option>';
        $Jifang_flow_list=$Huanan_jifang_flow->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_flow_list);$i++){
           $flowid=$Jifang_flow_list[$i]['id'];
           $name=$Jifang_flow_list[$i]['name'];

           if ($jifang_id==$flowid){
               $option.='<option value="'.$flowid.'" selected="selected">'.$name.'</option>';
            }else{
               //$option.='<option value="'.$flowid.'" >'.$name.'</option>';
            }

        }


        $portoption='';
        $Jifang_port_type_list=$Jifang_port_type->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_port_type_list);$i++){
           $tempid=$Jifang_port_type_list[$i]['id'];
           $tempname=$Jifang_port_type_list[$i]['name'];

           if ($type_id==$tempid){
               $portoption.='<option value="'.$tempid.'" selected="selected">'.$tempname.'</option>';
            }else{
               $portoption.='<option value="'.$tempid.'" >'.$tempname.'</option>';
            }

        }



        if ( IS_POST ) {

            empty($_REQUEST['jifang_id']) && $this->error('机房参数不能为空');


            $jifang_id=$_REQUEST['jifang_id'];
            $ip=$_REQUEST['ip'];
            $id=$_REQUEST['id'];
            $port=$_REQUEST['port'];
            $device_name=$_REQUEST['device_name'];
            $port_type=$_REQUEST['port_type'];

            $data['jifang_id'] = $_REQUEST['jifang_id'];
            $data['port'] = $_REQUEST['port'];

            $type_id=$_REQUEST['type_id'];
            $data['port_type_id'] = $type_id;
            $templist=$Jifang_port_type->where('id = "'.$type_id.'" ')->select();
            $port_typename=$templist[0]['name'];           
            $data['port_type'] = $port_typename;

            $data['device_name'] = $_REQUEST['device_name'];
            $data['ip'] = $_REQUEST['ip'];
            $data['desc'] = $_REQUEST['desc'];

            $checklist=$Huanan_jifang_param->where('jifang_id = "'.$jifang_id.'" and port = "'.$port.'" and ip = "'.$ip.'" and device_name = "'.$device_name.'" and port_type = "'.$port_type.'" ')->select();
            if (empty($checklist)){
                //$list=$Jingfang_param->data($data)->add();
                $list=$Huanan_jifang_param->where("id = $id")->setField($data);
                if (!empty($list)){
                    if (empty($jifang_id)){
                        $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Engineroomport/indexhuanan")}"; </script>');
                    }else{
                        $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist_huanan/jifangid/'.$jifang_id.'.html"; </script>');                        
                    }
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("重复操作，请重新编辑");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/edit_huanan_param/id/'.$id.'/jifang_id/'.$jifang_id.'.html"; </script>');
            }


        }

        //$this->assign("jfname",$jfname);
        $this->assign("param_find",$param_find);
        $this->assign("jifang_url_id",$jifang_url_id);
        $this->assign("option",$option);
        $this->assign("portoption",$portoption);
        $this->assign('url_flag','huanan'); //left flag
        $this->display();
    }



    public function del_huanan_param(){
    
        $Huanan_jifang_param = M('huanan_jifang_param');
        $id = $_REQUEST['id'];
        $jifang_id = $_REQUEST['jifang_id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $Huanan_jifang_param->where($where)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist_huanan/jifangid/'.$jifang_id.'.html"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist_huanan/jifangid/'.$jifang_id.'.html"; </script>');die;
        }
    }







    public function indexhuadong(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $Huadong_jifang_flow = M('huadong_jifang_flow');
        $Department = M('department');
        
        $jifangid = $_REQUEST['jifangid'];
        $depart_list = $Department->select();
        $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
        
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $condition = array();
        if (!IS_ROOT) {
            //$condition['depart_id'] = array('in',$arr);
        }

        if ($_GET['jifangid']) {
            $condition['id'] = $_GET['jifangid'];
        }

        $option='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/indexhuadong.html"   >请选择机房......</option>';
        $Jifang_flow_list=$Huadong_jifang_flow->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_flow_list);$i++){
           $flowid=$Jifang_flow_list[$i]['id'];
           $name=$Jifang_flow_list[$i]['name'];
           if ($jifangid==$flowid){
               $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/indexhuadong/jifangid/'.$flowid.'.html" selected="selected" >'.$name.'</option>';
           }else{
               $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/indexhuadong/jifangid/'.$flowid.'.html" >'.$name.'</option>';
           }
           

        }

        $list = $Huadong_jifang_flow->where($condition)->order('id desc')->select();
        
        $this->assign('option',$option);
        $this->assign('depart_arr_now',$depart_arr_now);
        $this->assign('list',$list);
        $this->assign('url_flag','huadong'); //left flag
        $this->display();
    }



    public function add_port_huadong(){
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Huadong_jifang_flow = M('huadong_jifang_flow');
        $name = $_REQUEST['name'];
        $jifang_url_id = $_REQUEST['jifangid'];

        if ( IS_POST ) {
            
            $data['name'] = $_REQUEST['name'];
            $checklist=$Huadong_jifang_flow->where('name = "'.$name.'" ')->select();
            if (empty($checklist)){
                $list=$Huadong_jifang_flow->data($data)->add();
                if (!empty($list)){
                    $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Engineroomport/indexhuadong")}"; </script>');
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("重复添加，请重新添加");window.location.href="{:U("Engineroomport/add_port_huadong")}"; </script>');
            }

        }

        $this->assign('jifang_url_id',$jifang_url_id); //left flag
        $this->assign('url_flag','huadong'); //left flag
        $this->display();
    }
    
    public function edit_port_huadong(){
        //echo Sec2Time(10000);die;
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Huadong_jifang_flow = M('huadong_jifang_flow');
        
        $id = $_REQUEST['id'];
        $Jifang_flow_find = $Huadong_jifang_flow->where("id = $id")->find();

        if ( IS_POST ) {

            $data['name'] = $_REQUEST['name'];
    
            $list=$Huadong_jifang_flow->where("id = $id")->setField($data);
            if (!empty($list)){
                $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Engineroomport/indexhuadong")}"; </script>');
                exit();
            }
        }
        
        $this->assign("id",$id);
        $this->assign("Jifang_flow_find",$Jifang_flow_find);
        $this->assign('url_flag','huadong'); //left flag
        $this->display();
    }
    

    
    public function del_port_huadong(){
    
        $Huadong_jifang_flow = M('huadong_jifang_flow');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $Huadong_jifang_flow->where($where)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Engineroomport/indexhuadong")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Engineroomport/indexhuadong")}"; </script>');die;
        }
    }



    public function portlist_huadong(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $Huadong_jifang_flow = M('huadong_jifang_flow');
        $Huadong_jifang_param = M('huadong_jifang_param');
        
        $jifangid = $_REQUEST['jifangid'];
        
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $condition = array();
        if (!IS_ROOT) {
            //$condition['depart_id'] = array('in',$arr);
        }

        if ($_GET['jifangid']) {
            $condition['jifang_id'] = $_GET['jifangid'];
        }

        $option='<option value="" selected="selected"  >请选择机房......</option>';
        $Jifang_flow_list=$Huadong_jifang_flow->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_flow_list);$i++){
           $flowid=$Jifang_flow_list[$i]['id'];
           $name=$Jifang_flow_list[$i]['name'];

           if ($jifangid==$flowid){
               $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist_huadong/jifangid/'.$flowid.'.html" selected="selected" >'.$name.'</option>';
           }else{
               $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist_huadong/jifangid/'.$flowid.'.html" >'.$name.'</option>';
           }
           

        }

        $list = $Huadong_jifang_param->where($condition)->order('id desc')->select();
        
        $this->assign('jifang_url_id',$jifangid);
        $this->assign('option',$option);
        $this->assign('depart_arr_now',$depart_arr_now);
        $this->assign('list',$list);
        $this->assign('url_flag','huadong'); //left flag
        $this->display();
    }



    public function add_huadong_param(){
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Traffic_portlist = M('traffic_portlist');
        $Huadong_jifang_flow = M('huadong_jifang_flow');
        $Huadong_jifang_param = M('huadong_jifang_param');
        $Jifang_huadong_port_type = M('jifang_huadong_port_type');
        $Ipdb_items = M('ipdb_items');

        $name = $_REQUEST['name'];
        $type_id = $_REQUEST['type_id'];
        $jifang_url_id = $_REQUEST['jifang_url_id'];
        

        $option='<option value="" >请选择机房......</option>';
        $Jifang_flow_list=$Huadong_jifang_flow->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_flow_list);$i++){
           $flowid=$Jifang_flow_list[$i]['id'];
           $name=$Jifang_flow_list[$i]['name'];

           if ($jifang_url_id==$flowid){
               $option.='<option value="'.$flowid.'" selected="selected">'.$name.'</option>';
            }else{
               $option.='<option value="'.$flowid.'" >'.$name.'</option>';
            }

        }

        $portoption='';
        $Jifang_huadong_port_type_list=$Jifang_huadong_port_type->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_huadong_port_type_list);$i++){
           $tempid=$Jifang_huadong_port_type_list[$i]['id'];
           $tempname=$Jifang_huadong_port_type_list[$i]['name'];

           if ($type_id==$tempid){
               $portoption.='<option value="'.$tempid.'" selected="selected">'.$tempname.'</option>';
            }else{
               $portoption.='<option value="'.$tempid.'" >'.$tempname.'</option>';
            }

        }


        if ( IS_POST ) {

            empty($_REQUEST['jifang_id']) && $this->error('机房参数不能为空');


            $jifang_id=$_REQUEST['jifang_id'];
            $ip=$_REQUEST['ip'];
            $port=$_REQUEST['port'];
            $device_name=$_REQUEST['device_name'];
            $type_id=$_REQUEST['type_id'];
            
            $listone=$Ipdb_items->where('ipv4 = "'.$ip.'" ')->select();
            $ipv4=$listone[0]['ipv4'];
            $hostid=$listone[0]['hostid'];
            empty($ipv4) && $this->error('设备在本系统中不存在！');
            empty($hostid) && $this->error('没加监控！');

            $list=$Traffic_portlist->where('host = "'.$ip.'" and portname="'.$port.'" ')->select();
            $port_hostid=$list[0]['hostid'];
            $initemid=$list[0]['initemid'];
            $outitemid=$list[0]['outitemid'];

            empty($port_hostid) && $this->error('端口不存在！');
            empty($initemid) && $this->error('没有授权或轮询为获取参数！');
            empty($outitemid) && $this->error('没有授权或轮询为获取参数！');


            $list=$Traffic_portlist->where('host = "'.$ip.'" and portname="'.$port.'" ')->select();
            $port_hostid=$list[0]['hostid'];
            $initemid=$list[0]['initemid'];
            $outitemid=$list[0]['outitemid'];


            $data['hostid'] = $hostid;
            $data['initemid'] = $initemid;
            $data['outitemid'] = $outitemid;

            $data['jifang_id'] = $_REQUEST['jifang_id'];
            $data['port'] = $_REQUEST['port'];

            $temp_find = $Jifang_huadong_port_type->where("id = $type_id")->find();
            $port_type=$temp_find['name'];

            $data['port_type'] = $port_type;
            $data['port_type_id'] = $type_id;
            $data['device_name'] = $_REQUEST['device_name'];
            $data['ip'] = $_REQUEST['ip'];
            $data['desc'] = $_REQUEST['desc'];



            $checklist=$Huadong_jifang_param->where('jifang_id = "'.$jifang_id.'" and port = "'.$port.'" and ip = "'.$ip.'" and device_name = "'.$device_name.'" and port_type_id = "'.$type_id.'" ')->select();
            if (empty($checklist)){
                $list=$Huadong_jifang_param->data($data)->add();
                if (!empty($list)){
                    if (empty($jifang_url_id)){
                        $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Engineroomport/indexhuadong")}"; </script>');
                    }else{
                        $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist_huadong/jifangid/'.$jifang_url_id.'.html"; </script>');                        
                    }
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("重复添加，请重新添加");window.location.href="{:U("Engineroomport/add_huadong_param")}"; </script>');
            }


        }

        $this->assign("jifang_url_id",$jifang_url_id);
        $this->assign("option",$option);
        $this->assign("portoption",$portoption);
        $this->assign('url_flag','huadong'); //left flag
        $this->display();
    }



    public function edit_huadong_param(){
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Traffic_portlist = M('traffic_portlist');
        $Huadong_jifang_flow = M('huadong_jifang_flow');
        $Huadong_jifang_param = M('huadong_jifang_param');
        $Jifang_huadong_port_type = M('jifang_huadong_port_type');
 

        $type_id = $_REQUEST['type_id'];
        $name = $_REQUEST['name'];
        $jifang_id = $_REQUEST['jifang_id'];

        $id = $_REQUEST['id'];
        empty($id) && $this->error('ID参数为空!');
        $param_find = $Huadong_jifang_param->where("id = $id")->find();
        $port_type_id=$param_find['port_type_id'];
 
        //$Jifang_flow_l=$Jifang_flow->where('id="'.$jifang_id.'"')->select();
        //$jfname=$Jifang_flow_l[0]['name'];


        //$option='<option value="" >请选择机房......</option>';
        $Jifang_flow_list=$Huadong_jifang_flow->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_flow_list);$i++){
           $flowid=$Jifang_flow_list[$i]['id'];
           $name=$Jifang_flow_list[$i]['name'];

           if ($jifang_id==$flowid){
               $option.='<option value="'.$flowid.'" selected="selected">'.$name.'</option>';
            }else{
               //$option.='<option value="'.$flowid.'" >'.$name.'</option>';
            }

        }

        $portoption='';
        $Jifang_huadong_port_type_list=$Jifang_huadong_port_type->order('id desc')->select(); 
        for($i=0;$i<count($Jifang_huadong_port_type_list);$i++){
           $tempid=$Jifang_huadong_port_type_list[$i]['id'];
           $tempname=$Jifang_huadong_port_type_list[$i]['name'];

           if ($port_type_id==$tempid){
               $portoption.='<option value="'.$tempid.'" selected="selected">'.$tempname.'</option>';
            }else{
               $portoption.='<option value="'.$tempid.'" >'.$tempname.'</option>';
            }

        }


        if ( IS_POST ) {

            empty($_REQUEST['jifang_id']) && $this->error('机房参数不能为空');


            $jifang_id=$_REQUEST['jifang_id'];
            $id=$_REQUEST['id'];
            $ip=$_REQUEST['ip'];
            $port=$_REQUEST['port'];
            $device_name=$_REQUEST['device_name'];
            $type_id=$_REQUEST['type_id'];
            $port_type_id=$_REQUEST['port_type_id'];

            $data['jifang_id'] = $_REQUEST['jifang_id'];
            $data['port'] = $_REQUEST['port'];

            $temp_find = $Jifang_huadong_port_type->where("id = $type_id")->find();
            $port_type=$temp_find['name'];

            $data['port_type'] = $port_type;
            $data['port_type_id'] = $_REQUEST['type_id'];
            $data['device_name'] = $_REQUEST['device_name'];
            $data['ip'] = $_REQUEST['ip'];
            $data['desc'] = $_REQUEST['desc'];

            $checklist=$Huadong_jifang_param->where('jifang_id = "'.$jifang_id.'" and port = "'.$port.'" and ip = "'.$ip.'" and device_name = "'.$device_name.'" and port_type_id = "'.$type_id.'" ')->select();
            if (empty($checklist)){
                //$list=$Jingfang_param->data($data)->add();
                $list=$Huadong_jifang_param->where("id = $id")->setField($data);
                if (!empty($list)){
                    if (empty($jifang_id)){
                        $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Engineroomport/indexhuadong")}"; </script>');
                    }else{
                        $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist_huadong/jifangid/'.$jifang_id.'.html"; </script>');                        
                    }
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("重复操作，请重新编辑");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/edit_huadong_param/id/'.$id.'/jifang_id/'.$jifang_id.'.html"; </script>');
            }


        }

        //$this->assign("jfname",$jfname);
        $this->assign("param_find",$param_find);
        $this->assign("jifang_url_id",$jifang_url_id);
        $this->assign("option",$option);
        $this->assign("portoption",$portoption);
        $this->assign('url_flag','huadong'); //left flag
        $this->display();
    }



    public function del_huadong_param(){
    
        $Huadong_jifang_param = M('huadong_jifang_param');
        
        $id = $_REQUEST['id'];
        $jifang_id = $_REQUEST['jifang_id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $Huadong_jifang_param->where($where)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist_huadong/jifangid/'.$jifang_id.'.html"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/portlist_huadong/jifangid/'.$jifang_id.'.html"; </script>');die;
        }
    }


















    public function index_port_huadong(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $Jifang_huadong_port_type = M('jifang_huadong_port_type');
        $Department = M('department');
        
        $depart_list = $Department->select();
        $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
        
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $condition = array();
        if (!IS_ROOT) {
            //$condition['depart_id'] = array('in',$arr);
        }





        $list = $Jifang_huadong_port_type->where($condition)->order('id desc')->select();
        


        $this->assign('option',$option);
        $this->assign('depart_arr_now',$depart_arr_now);
        $this->assign('list',$list);
        $this->assign('url_flag','huadong'); //left flag
        $this->display();
    }







    public function add_huadong_porttype(){
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Jifang_huadong_port_type = M('jifang_huadong_port_type');
        $name = $_REQUEST['name'];

        if ( IS_POST ) {
            
            $data['name'] = $_REQUEST['name'];
            $checklist=$Jifang_huadong_port_type->where('name = "'.$name.'" ')->select();
            if (empty($checklist)){
                $list=$Jifang_huadong_port_type->data($data)->add();
                if (!empty($list)){
                    $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Engineroomport/index_port_huadong")}"; </script>');
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("重复添加，请重新添加");window.location.href="{:U("Engineroomport/add_huadong_porttype")}"; </script>');
            }


        }

        $this->assign('jifang_url_id',$jifang_url_id); //left flag
        $this->assign('url_flag','huadong'); //left flag
        $this->display();
    }

    public function edit_huadong_porttype(){
        //echo Sec2Time(10000);die;
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }

        $Jifang_huadong_port_type = M('jifang_huadong_port_type');
        $jifang_id = $_REQUEST['jifangid'];
        $id = $_REQUEST['id'];
        $Jifang_flow_find = $Jifang_huadong_port_type->where("id = $id")->find();

        if ( IS_POST ) {

            $data['name'] = $_REQUEST['name'];
    
            $list=$Jifang_huadong_port_type->where("id = $id")->setField($data);
            if (!empty($list)){
                $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Engineroomport/index_port_huadong")}"; </script>');
                exit();
            }
        }
        
        $this->assign("id",$id);
        $this->assign("Jifang_flow_find",$Jifang_flow_find);
        $this->assign('url_flag','huadong'); //left flag
        $this->display();
    }



    public function del_huadong_porttype(){
    
        $Jifang_huadong_port_type = M('jifang_huadong_port_type');
        $id = $_REQUEST['id'];
        $jifang_id = $_REQUEST['jifangid'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $Jifang_huadong_port_type->where($where)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/index_port_huadong.html"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/index_port_huadong.html"; </script>');die;
        }
    }
































    public function index_port_huanan(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $Jifang_port_type = M('jifang_port_type');
        $Department = M('department');
        
        $depart_list = $Department->select();
        $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
        
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $condition = array();
        if (!IS_ROOT) {
            //$condition['depart_id'] = array('in',$arr);
        }





        $list = $Jifang_port_type->where($condition)->order('id desc')->select();
        


        $this->assign('option',$option);
        $this->assign('depart_arr_now',$depart_arr_now);
        $this->assign('list',$list);
        $this->assign('url_flag','huanan'); //left flag
        $this->display();
    }







    public function add_huanan_porttype(){
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Jifang_port_type = M('jifang_port_type');
        $name = $_REQUEST['name'];

        if ( IS_POST ) {
            
            $data['name'] = $_REQUEST['name'];
            $checklist=$Jifang_port_type->where('name = "'.$name.'" ')->select();
            if (empty($checklist)){
                $list=$Jifang_port_type->data($data)->add();
                if (!empty($list)){
                    $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Engineroomport/index_port_huanan")}"; </script>');
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("重复添加，请重新添加");window.location.href="{:U("Engineroomport/add_huanan_porttype")}"; </script>');
            }


        }

        $this->assign('jifang_url_id',$jifang_url_id); //left flag
        $this->assign('url_flag','huanan'); //left flag
        $this->display();
    }

    public function edit_huanan_porttype(){
        //echo Sec2Time(10000);die;
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Jifang_port_type = M('jifang_port_type');
        $jifang_id = $_REQUEST['jifangid'];
        $id = $_REQUEST['id'];
        $Jifang_flow_find = $Jifang_port_type->where("id = $id")->find();

        if ( IS_POST ) {

            $data['name'] = $_REQUEST['name'];
    
            $list=$Jifang_port_type->where("id = $id")->setField($data);
            if (!empty($list)){
                $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Engineroomport/index_port_huanan")}"; </script>');
                exit();
            }
        }
        
        $this->assign("id",$id);
        $this->assign("Jifang_flow_find",$Jifang_flow_find);
        $this->assign('url_flag','huanan'); //left flag
        $this->display();
    }



    public function del_huanan_porttype(){
    
        $Jifang_port_type = M('jifang_port_type');
        $id = $_REQUEST['id'];
        $jifang_id = $_REQUEST['jifangid'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $Jifang_port_type->where($where)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/index_port_huanan.html"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="'.__ROOT__.'/index.php?s=/Home/Engineroomport/index_port_huanan.html"; </script>');die;
        }
    }







    
    
    
}?>
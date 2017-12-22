<?php
namespace Home\Controller;
use Think\Controller;
class  ResourceController extends HomeController {
    
    public function roomlist(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $Web = M('web');
        $Department = M('department');
        

        $depart_list = $Department->select();
        $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');
        
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $condition = array();
        if (!IS_ROOT) {
            $condition['depart_id'] = array('in',$arr);
        }
        if ($_GET['name']) {
            $condition['name'] = array('like',"%".$_GET['name']."%");
        }

        $list = $Web->where($condition)->order('id asc')->select();
        


        $this->assign('depart_arr_now',$depart_arr_now);
        $this->assign('list',$list);
        $this->assign('url_flag','roomlist'); //left flag
        $this->display();
    }


    public function events(){
    
        if(!is_login()){
            $this->redirect("User/login");
        }
        $uid        =   is_login();
        $depart_id=single_depart_id($uid);


        $area = $_REQUEST['area'];
        
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }


        $events = M('zz_events');
        $Zz_events_type = M('zz_events_type');
        $list = $events->where("depart_id =".$depart_id." and verify = 1")->order(' start_time desc ')->select();

        for($i=0;$i<count($list);$i++){
              $event_typeid=$list[$i]['event_typeid'];
              $zzlist = $Zz_events_type->where("id =".$event_typeid." ")->select();
              $list[$i]['name']=$zzlist[0]['name'];
        }

        $this->assign('area',$area);
        $this->assign('event_list',$list);
        $this->assign("group_id",$group_id);
        $this->assign('url_flag','events'); //left flag
        $this->display();
    }
    

    public function add_events(){

        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $events = M('zz_events');
        $Zz_events_type = M('zz_events_type');
        $uid        =   is_login();
        $depart_id=single_depart_id($uid);

        $option='<option value="" >请选择事件类型</option>';
        $zzlist=$Zz_events_type->where('depart_id="'.$depart_id.'"')->order(' id desc')->select();
        for($i=0;$i<count($zzlist);$i++){
            $id=$zzlist[$i]['id'];
            $name=$zzlist[$i]['name'];
            $option.='<option value="'.$id.'"  >'.$name.'</option>';
        }  


        if ( IS_POST ) {
            $start_time = strtotime($_REQUEST['start_time']);
            
            $end_time = strtotime($_REQUEST['end_time']);
            if ($end_time < $start_time) {
                $this->error("结束时间不能小于开始时间");
            }
            $event_type = $_REQUEST['event_type'];
            empty($event_type) && $this->error('请选择事件类型项！');

            $data['uid'] = $uid;
            $data['depart_id'] = $depart_id;
            $data['group_id'] = $group_id;
            $data['event_typeid'] = $_REQUEST['event_type'];
            $data['start_time'] = $start_time;
            $data['end_time'] = $end_time;
            $data['effect_time'] = $_REQUEST['effect_time'];
            $data['effect_desc'] = $_REQUEST['effect_desc'];
            $data['real_reason'] = $_REQUEST['real_reason'];
            $data['deal_func'] = $_REQUEST['deal_func'];
            $data['has_notice'] = $_REQUEST['has_notice'];
            $data['is_important'] = $_REQUEST['is_important'];
            $data['need_help'] = $_REQUEST['need_help'];
            $data['pay_index'] = $_REQUEST['pay_index'];
            $data['effect_customer'] = $_REQUEST['effect_customer'];
            $data['number'] = $_REQUEST['number'];
            $data['line_range'] = $_REQUEST['line_range'];
            $data['gloption'] = $_REQUEST['gloption'];
            
            $list=$events->data($data)->add();
            if (!empty($list)){
                    //echo "资产添加成功";
                    $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Resource/events")}"; </script>');
                    exit();
            }
            
        }

        $arr = array('5','6','7','12');
        $this->assign("group_arr",$arr);
        $this->assign("option",$option);
        $this->assign("group_id",$group_id);
        $this->assign('url_flag','events'); //left flag
        $this->display();
    }
    
    public function edit_events(){
        //echo Sec2Time(10000);die;
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $events = M('zz_events');
        $Zz_events_type = M('zz_events_type');
        $uid        =   is_login();
        //$depart_id=single_depart_id($uid);

        $id = $_REQUEST['id'];
        $event_find = $events->where("id = $id")->find();
        $event_typeid=$event_find['event_typeid'];

        $depart_id=single_depart_id($uid);

        $option='<option value="" >请选择事件类型</option>';
        $zzlist=$Zz_events_type->where('depart_id="'.$depart_id.'"')->order(' id desc')->select();
        for($i=0;$i<count($zzlist);$i++){
            $tempid=$zzlist[$i]['id'];
            $name=$zzlist[$i]['name'];
            if ($event_typeid==$tempid){
               $option.='<option value="'.$tempid.'"  selected="selected">'.$name.'</option>';
            }else{
               $option.='<option value="'.$tempid.'"  >'.$name.'</option>'; 
            }
            
        } 
        
        if ( IS_POST ) {
            $start_time = strtotime($_REQUEST['start_time']);
    
            $end_time = strtotime($_REQUEST['end_time']);
            if ($end_time < $start_time) {
                $this->error("结束时间不能小于开始时间");
            }
            $event_type = $_REQUEST['event_type'];
            empty($event_type) && $this->error('请选择事件类型项！');    

            $data['group_id'] = $group_id;
            $data['event_typeid'] = $_REQUEST['event_type'];
            $data['start_time'] = $start_time;
            $data['end_time'] = $end_time;
            $data['effect_time'] = $_REQUEST['effect_time'];
            $data['effect_desc'] = $_REQUEST['effect_desc'];
            $data['real_reason'] = $_REQUEST['real_reason'];
            $data['deal_func'] = $_REQUEST['deal_func'];
            $data['has_notice'] = $_REQUEST['has_notice'];
            $data['is_important'] = $_REQUEST['is_important'];
            $data['need_help'] = $_REQUEST['need_help'];
            $data['pay_index'] = $_REQUEST['pay_index'];
            $data['effect_customer'] = $_REQUEST['effect_customer'];
            $data['number'] = $_REQUEST['number'];
            $data['line_range'] = $_REQUEST['line_range'];
            $data['gloption'] = $_REQUEST['gloption'];


            $list=$events->where("id = $id")->save($data);
            if (!empty($list)){
                //echo "资产添加成功";
                $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Resource/events")}"; </script>');
                exit();
            }
    
        }
        $this->assign("option",$option);
        $this->assign("event_find",$event_find);
        $this->assign("group_id",$group_id);
        $this->assign('url_flag','events'); //left flag
        $this->display();
    }
    

    //资产_ new
    public function veri_events(){
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $uid        =   is_login();
        $depart_id=single_depart_id($uid);


        $events = M('zz_events');
        $list = $events->where("verify =0 and depart_id='".$depart_id."' and uid!='".$uid."' ")->order(' start_time desc ')->select();
        if ( IS_POST ) {
            $ids = $_REQUEST['boxs'];
            empty($ids) && $this->error('没有选择项！');

            $idss = implode(',', $ids);
            $data['verify'] = 1;
            $ss = $events->where("id in ($idss)")->save($data);
            $this->show('<script type="text/javascript" >alert("审核成功");window.location.href="{:U("Resource/veri_events")}"; </script>');die;
        }
        $this->assign('username',$username);
        $this->assign('event_list',$list);
        $this->assign('url_flag','events'); //left flag
        $this->display();
    }
    
    public function del_events(){
    
        $events = M('zz_events');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $events->where($where)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Resource/events")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Resource/events")}"; </script>');die;
        }
    }
    
    



    public function events_manger(){
    
        if(!is_login()){
            $this->redirect("User/login");
        }
        $uid        =   is_login();
        
        $Zz_events_type = M('zz_events_type');       
        if (IS_ROOT) {
            $group_id = 5;
/*            $arr = get_hostgroup_by_uid($uid);
            $depart_id=implode(",",$depart_ids);
            $list = $Zz_events_type->where("depart_id in (".$depart_id.") ")->select();*/
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];

        }
        $depart_id=single_depart_id($uid);
        $list = $Zz_events_type->where("depart_id =".$depart_id." ")->select();
        

        if ( IS_POST ) {
            $name=$_REQUEST['name'];
            $adddata['name'] =$name;
            $adddata['depart_id'] = $depart_id;
            $ss = $Zz_events_type->where(" name= '".$name."' ")->select();
            if (empty($ss)){
                $list2=$Zz_events_type->data($adddata)->add();
                if (!empty($list2)){
                    $this->show('<script type="text/javascript" >alert("类型添加成功");window.location.href="{:U("Resource/events_manger")}"; </script>');die;
                }
            }else{
                $this->show('<script type="text/javascript" >alert("类型重复添加，请重新添加");window.location.href="{:U("Resource/events_manger")}"; </script>');
            }
            exit();
        }


        $this->assign('event_typelist',$list);
        $this->assign('url_flag','events'); //left flag
        $this->display();
    }










    public function edit_events_type(){
        //echo Sec2Time(10000);die;
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Zz_events_type = M('zz_events_type');
        $uid        =   is_login();
        //$depart_id=single_depart_id($uid);

        $id = $_REQUEST['id'];
        $event_find = $Zz_events_type->where("id = $id")->find();
        
        if ( IS_POST ) {

            $id=$_REQUEST['id'];
            empty($id) && $this->error('没有选择ID！');
            $name=$_REQUEST['name'];
            $data['name'] = $name;
            $list=$Zz_events_type->where(" id= '".$id."' ")->save($data);
            if (!empty($list)){
                $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Resource/events_manger")}"; </script>');
                exit();
            }
    
        }
        
        $this->assign("event_find",$event_find);
        $this->assign('url_flag','events'); //left flag
        $this->display();
    }












    public function del_events_type(){
    
        $Zz_events_type = M('zz_events_type');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $Zz_events_type->where($where)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Resource/events_manger")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Resource/events_manger")}"; </script>');die;
        }
    }










    public function gloption_manger(){
    
        if(!is_login()){
            $this->redirect("User/login");
        }
        $uid        =   is_login();
        
        $Zz_events_gloption = M('zz_events_gloption');       
        if (IS_ROOT) {
            $group_id = 5;
/*            $arr = get_hostgroup_by_uid($uid);
            $depart_id=implode(",",$depart_ids);
            $list = $Zz_events_type->where("depart_id in (".$depart_id.") ")->select();*/
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];

        }
        $depart_id=single_depart_id($uid);
        $list = $Zz_events_gloption->where("depart_id =".$depart_id." ")->select();
        

        if ( IS_POST ) {
            $name=$_REQUEST['name'];
            $adddata['name'] =$name;
            $adddata['depart_id'] = $depart_id;
            $ss = $Zz_events_gloption->where(" name= '".$name."' ")->select();
            if (empty($ss)){
                $list2=$Zz_events_gloption->data($adddata)->add();
                if (!empty($list2)){
                    $this->show('<script type="text/javascript" >alert("光缆运营商添加成功");window.location.href="{:U("Resource/gloption_manger")}"; </script>');die;
                }
            }else{
                $this->show('<script type="text/javascript" >alert("光缆运营商重复添加，请重新添加");window.location.href="{:U("Resource/gloption_manger")}"; </script>');
            }
            exit();
        }


        $this->assign('event_gloptionlist',$list);
        $this->assign('url_flag','events'); //left flag
        $this->display();
    }










    public function edit_events_gloption(){
        //echo Sec2Time(10000);die;
        if (IS_ROOT) {
            $group_id = 5;
        }else{
            $group_id = $_SESSION['user_auth']['usergroup_id'];
        }
        $Zz_events_gloption = M('zz_events_gloption');
        $uid        =   is_login();
        //$depart_id=single_depart_id($uid);

        $id = $_REQUEST['id'];
        $event_find = $Zz_events_gloption->where("id = $id")->find();
        
        if ( IS_POST ) {

            $id=$_REQUEST['id'];
            empty($id) && $this->error('没有选择ID！');
            $name=$_REQUEST['name'];
            $data['name'] = $name;
            $list=$Zz_events_gloption->where(" id= '".$id."' ")->save($data);
            if (!empty($list)){
                $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Resource/gloption_manger")}"; </script>');
                exit();
            }
    
        }
        
        $this->assign("event_find",$event_find);
        $this->assign('url_flag','events'); //left flag
        $this->display();
    }






    public function del_events_gloption(){
    
        $Zz_events_gloption = M('zz_events_gloption');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
    
        $where['id'] = $id;
        $del = $Zz_events_gloption->where($where)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Resource/gloption_manger")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Resource/gloption_manger")}"; </script>');die;
        }
    }




    public function ajax_events_gloption(){
    
        $Zz_events_gloption = M('zz_events_gloption');
        $uid        =   is_login();
        $depart_id=single_depart_id($uid);

        $name = $_REQUEST['name'];
        empty($name) && $this->error('请输入搜索关键字');

        //$where['depart_id'] = $depart_id;
        //$where['name'] = array('like',"%".$name."%");
        $list = $Zz_events_gloption->where(' depart_id="'.$depart_id.'" and  name like "%'.$name.'%" ')->select();
        //echo ' depart_id="'.$depart_id.'" and  name like "%'.$name.'%" ';
        //var_dump($list);
        //$json_list = json_encode($list);
        $this->ajaxReturn($list);
         
        //$this->assign('json_list',$json_list);

    }






    
    public function add_web(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $Web = M('web');
        $Department = M('department');
/*        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $map_room['depart_id'] = array('in',$arr);
        $room_list = $Room->where($map_room)->select();*/

        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        if (!IS_ROOT) {
            $map_depart['id'] = array('in',$arr);
        }
        $depart_list = $Department->where($map_depart)->select();


        if ( IS_POST ) {
        
            $name= I('post.name');
            $depart_id= I('post.depart_id');
            $data['name'] = $name;
            $data['depart_id'] = $depart_id;
            $checklist=$Web->where('name = "'.$name.'" ')->select();
            if (empty($checklist)){
                $list=$Web->data($data)->add();
                if (!empty($list)){
                    //echo "资产添加成功";
                    $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Resource/roomlist")}"; </script>');
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("重复添加，请重新添加");window.location.href="{:U("Resource/add_web")}"; </script>');
            }
        }
        
        $this->assign('depart_list',$depart_list);
        $this->assign('list',$list);
        $this->assign('url_flag','roomlist'); //left flag
        $this->display();
    }



    public function edit_web() {

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');
        $Web = M('web');
        $Department = M('department');

        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        if (!IS_ROOT) {
            $map_depart['id'] = array('in',$arr);
        }
        $depart_list = $Department->where($map_depart)->select();

        $web_list = $Web->where("id=$id")->find();


        if (IS_POST) {
             $name = $_REQUEST['name'];
             $id = $_REQUEST['id'];
             $depart_id= I('post.depart_id');
             empty($id) && $this->error('请输入ID');

             $list=$Web->where('id="'.$id.'"  ')->setField(array('name'=>$name,'depart_id'=>$depart_id));

             $this->show('<script type="text/javascript" >alert("修改成功!");window.location.href="{:U("Resource/roomlist")}"; </script>');
             exit();
        }


        $this->assign('depart_list',$depart_list);
        $this->assign('web_list',$web_list);
        $this->assign('url_flag','roomlist'); //left flag
        $this->display();
    }



    public function del_web(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $Web = M('web');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');

        $where_web['id'] = $id;
        $del = $Web->where($where_web)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Resource/roomlist")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Resource/roomlist")}"; </script>');die;
        }
    }





    public function cactiindex(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();
        $Resource = M('resource');
        $Web = M('web');
        $Department = M('department');

        $depart_list = $Department->select();
        $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');

        $web_list = $Web->select();
        $web_arr_now = deal_array($web_list, 'id', 'name');

        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        //$map_web['depart_id'] = array('in',$arr);
        if (!IS_ROOT) {
            $map_web['depart_id'] = array('in',$arr);
        }
        $web_list = $Web->where($map_web)->select();

        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $condition = array();
        if (!IS_ROOT) {
            $condition['dep_id'] = array('in',$arr);
        }
        if ($_GET['name']) {
            $condition['name'] = array('like',"%".$_GET['name']."%");
        }
        if ($_GET['webid']) {
            $condition['webid'] = $_GET['webid'];
        }

        $map['webid'] = $_GET['webid'];
        $map['name'] = $_GET['name'];
        foreach($map as $key=>$val) {
            $p->parameter .= "$key=".urlencode($val)."&";
        }

        $count = $Resource->where($condition)->field("id")->count();
        $page = new \Think\Page($count,10);
        $list=$Resource->where($condition)->order('id desc')->limit($page->firstRow,$page->listRows)->select();

        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        
        $this->assign('page',$page->show());

        $this->assign('p',$_GET['p']);
        $this->assign('web_list',$web_list);
        $this->assign('web_arr_now',$web_arr_now);
        $this->assign('depart_arr_now',$depart_arr_now);
        $this->assign('list',$list);
        $this->assign('url_flag','cactiindex'); //left flag
        $this->display();
    }



    public function add_cacti(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $Web = M('web');
        $Webtype = M('webtype');
        $Resource = M('resource');
        $Department = M('department');
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $map_web['depart_id'] = array('in',$arr);
        $web_list = $Web->where($map_web)->select();

        if (!IS_ROOT) {
            $map_depart['id'] = array('in',$arr);
        }
        $depart_list = $Department->where($map_depart)->select();

        $list2=$Webtype->field('id,webid,typename,depart_id')->select();
        $jsonlist2 = json_encode($list2);

        if ( IS_POST ) {

            $name= I('post.name');
            //$webtype= I('post.webtype');
            $flowtype= I('post.flowtype');
            $depart_id= I('post.depart_id');
            $other_name= I('post.other_name');
            $url= I('post.url');
            $local_graph_id= I('post.local_graph_id');
            $webid= I('post.webid');
            $meta= I('post.meta');
            $rrd= I('post.rrd');
            $flag= I('post.flag');
            $purchasing_bandwidth= I('post.purchasing_bandwidth');

            empty($webid) && $this->error('请选择流量图所在页面!');
            empty($depart_id) && $this->error('请输入流量图管理部门!');
            empty($flowtype) && $this->error('请输入流量图统计类型!');

            $data['name'] = $name;
            //$data['webtype'] = $webtype;
            $data['flowtype'] = $flowtype;
            $data['dep_id'] = $depart_id;
            $data['other_name'] = $other_name;
            $data['webid'] = $webid;
            $data['local_graph_id'] = $local_graph_id;
            $data['url'] = $url;
            $data['uid'] = $uid;
            $data['meta'] = $meta;
            $data['flag'] = $flag;
            $data['rrdpath'] = trim($rrd);
            $data['purchasing_bandwidth'] = trim($purchasing_bandwidth);

            $checklist=$Resource->where('name = "'.$name.'" and webid = "'.$webid.'" ')->select();
            if (empty($checklist)){
                $list=$Resource->data($data)->add();
                if (!empty($list)){
                    //echo "资产添加成功";
                    $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Resource/cactiindex")}"; </script>');
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("该机房下流量图名称重复添加，请重新添加");window.location.href="{:U("Resource/add_cacti")}"; </script>');
            }
        }
        
        $this->assign('depart_list',$depart_list);
        $this->assign('web_list',$web_list);
        $this->assign('list',$list);
        $this->assign('jsonlist2',$jsonlist2);
        $this->assign('url_flag','cactiindex'); //left flag
        $this->display();
    }



    public function edit_cacti() {

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $p = $_REQUEST['p'];
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');

        $Web = M('web');
        $Webtype = M('webtype');
        $Resource = M('resource');
        $Department = M('department');

        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $map_dep['id'] = array('in',$arr);
        $depart_list = $Department->where($map_dep)->select();
        
        if (!IS_ROOT) {
            $map_web['depart_id'] = array('in',$arr);
        }
        $web_list = $Web->where($map_web)->select();
        $resource_info = $Resource->where('id="'.$id.'"  ')->find();

        if (!IS_ROOT) {
            $map_webtype['depart_id'] = array('in',$arr);
        }
        $webtype_list = $Webtype->where($map_webtype)->select();
        $webtype_arr_now = deal_array($webtype_list, 'id', 'typename');

        $list2=$Webtype->field('id,webid,typename,depart_id')->select();
        $jsonlist2 = json_encode($list2);

        if (IS_POST) {

             $p = $_REQUEST['p'];
             $id = $_REQUEST['id'];
             $url = $_REQUEST['url'];
             $flag = $_REQUEST['flag'];
             $typeid= I('post.typeid');
             $flowtype= I('post.flowtype');
             $depart_id= I('post.depart_id');
             $name = $_REQUEST['name'];
             $other_name = $_REQUEST['other_name'];
             $webid = $_REQUEST['webid'];
             $local_graph_id= $_REQUEST['local_graph_id'];
             $meta = $_REQUEST['meta'];
             $sortid = $_REQUEST['sortid'];
             $rrd = trim($_REQUEST['rrd']);
             $purchasing_bandwidth = trim($_REQUEST['purchasing_bandwidth']);

             empty($id) && $this->error('流量图参数ID缺失!');
             empty($webid) && $this->error('请选择流量图所在页面!');
             empty($typeid) && $this->error('请选择Web页面二级分类!');
             empty($depart_id) && $this->error('请输入流量图管理部门!');
             empty($flowtype) && $this->error('请输入流量图统计类型!');

             $Resource->where('id="'.$id.'" ')->setField(array('flag'=>$flag,'typeid'=>$typeid,'purchasing_bandwidth'=>$purchasing_bandwidth,'flowtype'=>$flowtype,'webtype'=>$webtype,'name'=>$name,'local_graph_id'=>$local_graph_id,'rrdpath'=>$rrd,'roomid'=>$roomid,'sortid'=>$sortid,'meta'=>$meta,'uid'=>$uid,'other_name'=>$other_name,'url'=>$url,'dep_id'=>$depart_id));
             $this->show('<script type="text/javascript" >alert("修改成功!");window.location.href="{:U(\"Resource/cactiindex?webid='.$webid.'&p='.$p.'\")}"; </script>');
             exit();
        }


        $this->assign('p',$p);
        $this->assign('id',$id);
        $this->assign('web_list',$web_list);
        $this->assign('jsonlist2',$jsonlist2);
        $this->assign('webtype_arr_now',$webtype_arr_now);
        $this->assign('resource_info',$resource_info);
        $this->assign('depart_list',$depart_list);
        $this->assign('url_flag','cactiindex'); //left flag
        $this->display();
    }



public function del_cacti(){

    if(!is_login()){
      $this->redirect("User/login");
    }   
    $uid        =   is_login();

    $Resource = M('resource');
    $id = $_REQUEST['id'];
    empty($id) && $this->error('请输入ID');

    $where_resource['id'] = $id;
    $del = $Resource->where($where_resource)->delete();
    if ($del) {
        $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Resource/cactiindex")}"; </script>');die;
    }else{
        $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Resource/cactiindex")}"; </script>');die;
    }
}



public function  cactimap(){

  if(!is_login()){
      $this->redirect("User/login");
  }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');

  $Web = M('web');
  $Webtype = M('webtype');
  $Resource = M('resource');
  $User = M('user');

  $webid= $_REQUEST['webid'];
  $webids=implode(',',$webid);

if (!empty($webids)){
    $webtype_list = $Webtype->where('id in ('.$webids.')')->select();
    $checkbox_webid=$webtype_list[0]['webid'];
}

  //-----------------------------获取动态机房，房间数据
  $showlabel='';

  $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
  //$map_web['depart_id'] = array('in',$arr);
  if (!IS_ROOT) {
      $map_web['depart_id'] = array('in',$arr);
  }
  $web_list = $Web->where($map_web)->select();
  $rand=100000;
  for($i=0;$i<count($web_list);$i++){
      $web_id=$web_list[$i]['id'];
      $web_name=$web_list[$i]['name'];
      //echo $room_id."----".var_dump($roomid)."<br />";
      if (in_array($web_id, $webid)){
         $checked='checked';  
      }else{
        if (empty($webid)){
           //$checked='checked';
        }else{
           $checked='';
        }
      }

      $showlabel.='<div class="tagbox" style="text-align:left; padding-left:0px;"><label>';
      $showlabel.='<input type="checkbox" name="webid[]" class="0" value="'.$rand.'" '.$checked.' onclick="checkAll(this)">';//value="'.$web_id.'"
      $showlabel.=$web_name;
      $showlabel.='</label>';//<a  href="" "#"=""> - </a>
      $showlabel.='<div >';

      $sublist=$Webtype->where(' webid="'.$web_id.'" ')->order('id asc')->select();
      for($ii=0;$ii<count($sublist);$ii++){
        $sublid=$sublist[$ii]['id'];
        $typename=$sublist[$ii]['typename'];

        if (in_array($sublid, $webid)){
           $checked='checked';  
        }else{
          if (empty($webid)){
             //$checked='checked';
          }else{
             $checked='';
          }
        }

        $showlabel.='<div class="tagbox" style="text-align:left; padding-left:16px;">';
        $showlabel.='<label><input type="checkbox" name="webid[]" class="1" value="'.$sublid.'" '.$checked.' onclick="checkAll(this)" >';
        $showlabel.=$typename;
        $showlabel.='</label></div>';
      }      

      $showlabel.='</div></div>';

    $rand++;

  }




if(!empty($webids)){

  $list = $Resource->where('typeid in ('.$webids.') ')->group('other_name')->order('id asc')->select();
  $showmenu=array();
  $tr='';
  for($i=0;$i<count($list);$i++){

    $id=$list[$i]['id'];
    $name=$list[$i]['name'];
    $other_name=$list[$i]['other_name'];
    $url=$list[$i]['url'];
    $rrd=$list[$i]['rrdpath'];
    $tempwebid=$list[$i]['webid'];
    $tempweblist = $Web->where('id = "'.$tempwebid.'" ')->order('id desc')->find();
    
    $list[$i]['web_name']=$tempweblist['name'];

    unset($tr);
    unset($rlist);
    $rlist = $Resource->where('other_name = "'.$other_name.'" ')->order('sortid,id asc')->select();
    for($ii=0;$ii<count($rlist);$ii++){
        $rid=$rlist[$ii]['id'];
        $rurl=$rlist[$ii]['url'];
        $rname=$rlist[$ii]['name'];
        $tr.='<a href="'.$rurl.'" target="_blank">'.$rname.'</a>&nbsp;&nbsp;<br />';
    }

    $list[$i]['rlist']=$tr;

    

  }

}



if (IS_POST) {

    $stime = $_REQUEST['stime'];
    $end = $_REQUEST['end'];
    $webid= $_REQUEST['webid'];
    $webids=implode(',',$webid);



}



  $this->assign('webid',$webid);
  $this->assign('checkbox_webid',$checkbox_webid);
  $this->assign('url_flag','cactimap');
  $this->assign('json_showmenu',$json_showmenu);
  $this->assign('tr',$tr);
  $this->assign('list',$list);
  $this->assign('showlabel',$showlabel);
  $this->display();  
}




public function cactimenulist(){

    header("Content-type: text/html; charset=utf-8");
    if(!is_login()){
        $this->redirect("User/login");
    }   
    $uid        =   is_login();
    $nickname = M('Member')->getFieldByUid($uid, 'nickname');

    $Cacti_webmenu = M('cacti_webmenu');

    $condition = array();
    if ($_GET['title']) {
        $condition['title'] = array('like',"%".$_GET['title']."%");
    }

    $cacti_webmenu_list=$Cacti_webmenu->where($condition)->select();
    $deal_list = getTree($cacti_webmenu_list);

    
    $this->assign('list',$deal_list);
    $this->assign('url_flag','cactimenulist'); //left flag
    $this->display();

}




public function add_cactiwebmenu(){

    header("Content-type: text/html; charset=utf-8");
    if(!is_login()){
        $this->redirect("User/login");
    }   
    $uid        =   is_login();
    $nickname = M('Member')->getFieldByUid($uid, 'nickname');

    $pidoption="";
    $Cacti_webmenu = M('cacti_webmenu');
    $list=$Cacti_webmenu->where('pid=0')->order(' id asc')->select();

    for($i=0;$i<count($list);$i++){
        $id=$list[$i]['id'];
        $title=$list[$i]['title'];

        $pidoption.='<option value="'.$id.'"  >'.$title.'</option>';
        $list2=$Cacti_webmenu->where('pid="'.$id.'" ')->order(' id asc')->select();
        for($ii=0;$ii<count($list2);$ii++){
            $subid=$list2[$ii]['id'];
            $subtitle=$list2[$ii]['title'];
            $pidoption.='<option value="'.$subid.'"  >&nbsp;&nbsp;└'.$subtitle.'</option>';

            $list3=$Cacti_webmenu->where('pid="'.$subid.'" ')->order(' id asc')->select();
            for($iii=0;$iii<count($list3);$iii++){
                $threeid=$list3[$iii]['id'];
                $threetitle=$list3[$iii]['title'];
                $pidoption.='<option value="'.$threeid.'"  >&nbsp;&nbsp;&nbsp;&nbsp;└'.$threetitle.'</option>';
            }            

        }

    }


    if(IS_POST){
      $title=I('post.title');
      $map['title']=I('post.title');
      $map['pid']=I('post.pid');


      $list = $Cacti_webmenu->where('title="'.$title.'"')->select();
      if(!empty($list)){
          $this->error('标题已经存在!');
      }
      if($Cacti_webmenu->add($map)){
          $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("cactimenulist")}"; </script>');
          exit();
      }else{
          $this->show('<script type="text/javascript" >alert("添加失败");window.location.href="{:U("cactimenulist")}"; </script>');
          exit();
      }

    }
    $this->assign('uid',$uid);
    $this->assign('nickname',$nickname);
    $this->assign('pidoption',$pidoption);
    $this->assign('url_flag','cactimenulist'); //left flag
    $this->display();
  }






public function edit_cactiwebmenu(){

    if(!is_login()){
      $this->redirect("User/login");
    }  
    $uid        =   is_login();
    $nickname = M('Member')->getFieldByUid($uid, 'nickname');


    $id   =  $_REQUEST['id']; 
    empty($id) && $this->error('id不能为空');

    $pid   =  $_REQUEST['pid']; 
    $Cacti_webmenu = M('cacti_webmenu');

    $list=$Cacti_webmenu->where('id="'.$id.'"')->select();
    $title=$list[0]['title']; 
    $sort=$list[0]['sort']; 
    $pid=$list[0]['pid']; 
    if (empty($pid)){
        $pidstr='无';
    }else{
        $list2=$Cacti_webmenu->where('id="'.$pid.'"')->select();
        $pidstr=$list2[0]['title'];
    }
    $url=$list[0]['url']; 
    $group=$list[0]['group']; 

//-----------------------------------------------------菜单下拉----------------------------------------------------------
    $pidoption="";
    $list=$Cacti_webmenu->where('pid=0')->order(' id asc')->select();
    for($i=0;$i<count($list);$i++){
        $tempid=$list[$i]['id'];
        $temptitle=$list[$i]['title'];
        
        if ($tempid==$pid){
            $pidoption.='<option value="'.$tempid.'" selected="selected" >'.$temptitle.'</option>';
        }else{
            $pidoption.='<option value="'.$tempid.'"  >'.$temptitle.'</option>';
        }

        $list2=$Cacti_webmenu->where('pid="'.$tempid.'" ')->order(' id asc')->select();
        for($ii=0;$ii<count($list2);$ii++){
            $subid=$list2[$ii]['id'];
            $subpid=$list2[$ii]['pid'];
            $subtitle=$list2[$ii]['title'];

            if ($subid==$pid){
                $pidoption.='<option value="'.$subid.'" selected="selected" >&nbsp;&nbsp;└'.$subtitle.'</option>';
            }else{
                $pidoption.='<option value="'.$subid.'"  >&nbsp;&nbsp;└'.$subtitle.'</option>';
            }
            
            $list3=$Cacti_webmenu->where('pid="'.$subid.'" ')->order(' id asc')->select();
            for($iii=0;$iii<count($list3);$iii++){
                $threeid=$list3[$iii]['id'];
                $threetitle=$list3[$iii]['title'];
                if ($threeid==$pid){
                    $pidoption.='<option value="'.$threeid.'" selected="selected" >&nbsp;&nbsp;&nbsp;&nbsp;└'.$threetitle.'</option>';
                }else{
                    $pidoption.='<option value="'.$threeid.'"  >&nbsp;&nbsp;&nbsp;&nbsp;└'.$threetitle.'</option>';
                }
            }            
        }



    }

//---------------------------------------------------------------------------------------------------------------



    if(IS_POST){

        $id=I('post.id');
        $pid   =  I('post.pid');
        empty($id) && $this->error('id不能为空');
        $data['title']=I('post.title');
        $data['pid']=$pid;

        $list3=$Cacti_webmenu->where('id="'.$id.'"')->setField($data);
        if($list3){
          if (!empty($id) && !empty($pid)){
              $this->show('<script type="text/javascript" >alert("修改成功！");window.location.href="{:U(\"cactimenulist?id='.$id.'&pid='.$pid.'\")}"; </script>');
          }else{
              $this->show('<script type="text/javascript" >alert("修改成功！");window.location.href="{:U("cactimenulist")}"; </script>');
          }
          exit();
        }else{
          if (!empty($id) && !empty($pid)){
              $this->show('<script type="text/javascript" >alert("修改失败！");window.location.href="{:U(\"cactimenulist?id='.$id.'&pid='.$pid.'\")}"; </script>');
          }else{
              $this->show('<script type="text/javascript" >alert("修改失败！");window.location.href="{:U("cactimenulist")}"; </script>');
          }
          exit();
        }
    
    }

    $this->assign('uid',$uid);
    $this->assign('nickname',$nickname);
    $this->assign('title',$title);
    $this->assign('sort',$sort);
    $this->assign('id',$id);
    $this->assign('group',$group);
    $this->assign('pidstr',$pidstr);
    $this->assign('url',$url);
    $this->assign('pid',$pid);
    $this->assign('pidoption',$pidoption);
    $this->assign('url_flag','menu'); //left flag
    $this->display();
  }


public function del_cactiwebmenu(){

    if(!is_login()){
      $this->redirect("User/login");
    }   
    $uid        =   is_login();

    $Cacti_webmenu = M('cacti_webmenu');
    $id = $_REQUEST['id'];
    empty($id) && $this->error('请输入ID');

    $where_webmenu['id'] = $id;
    $del = $Cacti_webmenu->where($where_webmenu)->delete();
    if ($del) {
        $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Resource/cactimenulist")}"; </script>');die;
    }else{
        $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Resource/cactimenulist")}"; </script>');die;
    }
}




public function webtype(){

    if(!is_login()){
      $this->redirect("User/login");
    }   
    $uid        =   is_login();
    $Webtype = M('webtype');
    $Web = M('web');
    $Department = M('department');

    $web_list = $Web->select();
    $web_arr_now = deal_array($web_list, 'id', 'name');

    $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
    $condition = array();
    if (!IS_ROOT) {
        $condition['depart_id'] = array('in',$arr);
    }

    if ($_GET['typename']) {
        $condition['typename'] = array('like',"%".$_GET['typename']."%");
    }

    $map['typename'] = $_GET['typename'];
    foreach($map as $key=>$val) {
        $p->parameter .= "$key=".urlencode($val)."&";
    }

    $count = $Webtype->where($condition)->field("id")->count();
    $page = new \Think\Page($count,10);
    $list=$Webtype->where($condition)->order('id desc')->limit($page->firstRow,$page->listRows)->select();

    $page->setConfig('header','共');
    $page->setConfig('first','«');
    $page->setConfig('last','»');
    $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
    
    $this->assign('page',$page->show());

    $this->assign('list',$list);
    $this->assign('web_arr_now',$web_arr_now);
    $this->assign('list',$list);
    $this->assign('url_flag','webtype'); //left flag
    $this->display();
}



public function add_webtype(){

    if(!is_login()){
        $this->redirect("User/login");
    }   
    $uid        =   is_login();
    $nickname = M('Member')->getFieldByUid($uid, 'nickname');

    $Web = M('web');
    $Webtype = M('webtype');
    $Department = M('department');

    $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
    $condition = array();
    if (!IS_ROOT) {
        $condition['depart_id'] = array('in',$arr);
    }
    $list=$Web->where($condition)->select();
    $weboption='';
    for($i=0;$i<count($list);$i++){      
        $webid=$list[$i]['id'];
        $webname=$list[$i]['name'];
        $weboption.='<option value ="'.$webid.'" >'.$webname.'</option>';
    }

    if (!IS_ROOT) {
        $map_depart['id'] = array('in',$arr);
    }
    $depart_list = $Department->where($map_depart)->select();

    if(IS_POST){

      $typename=I('post.typename');
      $webid=I('post.webid');
      $depart_id=I('post.depart_id');

      empty($typename) && $this->error('请输入分类名称');
      empty($webid) && $this->error('请输入所在页面');
      empty($depart_id) && $this->error('请输入所属部门');

      $map['typename']=I('post.typename');
      $map['webid']=I('post.webid');
      $map['depart_id']=I('post.depart_id');

      $list = $Webtype->where('typename="'.$typename.'"')->select();
      if(!empty($list)){
          $this->error('名称已经存在!');
      }
      if($Webtype->add($map)){
          $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("webtype")}"; </script>');
          exit();
      }else{
          $this->show('<script type="text/javascript" >alert("添加失败");window.location.href="{:U("add_webtype")}"; </script>');
          exit();
      }

    }

    $this->assign('uid',$uid);
    $this->assign('nickname',$nickname);
    $this->assign('depart_list',$depart_list);
    $this->assign('weboption',$weboption);
    $this->assign('url_flag','webtype'); //left flag
    $this->display();
  }



public function edit_webtype() {

    if(!is_login()){
      $this->redirect("User/login");
    }   
    $uid        =   is_login();

    $p = $_REQUEST['p'];
    $id = $_REQUEST['id'];
    empty($id) && $this->error('请输入ID');

    $Web = M('web');
    $Webtype = M('webtype');
    $Department = M('department');
    
    $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
    if (!IS_ROOT) {
        $map_dep['id'] = array('in',$arr);
    }
    $depart_list = $Department->where($map_dep)->select();
    $map_web['depart_id'] = array('in',$arr);
    if (!IS_ROOT) {
        $map_web['depart_id'] = array('in',$arr);
    }
    $web_list = $Web->where($map_web)->select();

    $webtype_info = $Webtype->where('id="'.$id.'"  ')->find();
    $webid=$webtype_info['webid'];

    $condition = array();
    if (!IS_ROOT) {
        $condition['depart_id'] = array('in',$arr);
    }
    $list=$Web->where($condition)->select();
    $weboption='';
    for($i=0;$i<count($list);$i++){      
        $tempwebid=$list[$i]['id'];
        $tempwebname=$list[$i]['name'];
        if ($webid==$tempwebid){
            $weboption.='<option value="'.$tempwebid.'" selected="selected" >'.$tempwebname.'</option>';
        }else{
            $weboption.='<option value="'.$tempwebid.'"  >'.$tempwebname.'</option>';
        }
    }


    if (!IS_ROOT) {
        $map_depart['id'] = array('in',$arr);
    }
    $depart_list = $Department->where($map_depart)->select();    

    if (IS_POST) {

        $p = $_REQUEST['p'];
        $id = $_REQUEST['id'];
        $typename=I('post.typename');
        $webid=I('post.webid');
        $depart_id=I('post.depart_id');

        empty($id) && $this->error('请输入ID');
        empty($typename) && $this->error('请输入分类名称');
        empty($webid) && $this->error('请输入所在页面');
        empty($depart_id) && $this->error('请输入所属部门');

        $Webtype->where('id="'.$id.'" ')->setField(array('typename'=>$typename,'webid'=>$webid,'depart_id'=>$depart_id));
        $this->show('<script type="text/javascript" >alert("修改成功!");window.location.href="{:U(\"Resource/webtype?p='.$p.'\")}"; </script>');
        exit();
    }


    $this->assign('p',$p);
    $this->assign('id',$id);
    $this->assign('web_list',$web_list);
    $this->assign('depart_list',$depart_list);
    $this->assign('weboption',$weboption);
    $this->assign('webtype_info',$webtype_info);
    $this->assign('url_flag','webtype'); //left flag
    $this->display();
}











public function del_webtype(){

    if(!is_login()){
      $this->redirect("User/login");
    }   
    $uid        =   is_login();

    $Webtype = M('webtype');
    $id = $_REQUEST['id'];
    empty($id) && $this->error('请输入ID');

    $where_webtype['id'] = $id;
    $del = $Webtype->where($where_webtype)->delete();
    if ($del) {
        $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Resource/webtype")}"; </script>');die;
    }else{
        $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Resource/webtype")}"; </script>');die;
    }

}
















    
    
    
}?>
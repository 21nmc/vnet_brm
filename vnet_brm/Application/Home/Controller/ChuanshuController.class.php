<?php
namespace Home\Controller;
use Think\Controller;
class  ChuanshuController extends HomeController {
    
    public function chuanshu(){

        $Cs_event = M('cs_event');
        $Department = M('department');

        
        $area = $_REQUEST['area'];
        
        $condition = array();
        if ($_GET['event_type']) {
            $condition['event_type'] = array('like',"%".$_GET['event_type']."%");
        }
        if ($_GET['area']) {
            $condition['area'] = $_GET['area'];
        }

        $count = $Cs_event->where($condition)->field("id")->count();
        $page = new \Think\Page($count,10);
        $list=$Cs_event->where($condition)->order('id desc')->limit($page->firstRow,$page->listRows)->select();

//----------------------------------------------------------------------------------------------------
        $uid = is_login();
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        if (!IS_ROOT) {
            $map_depart['id'] = array('in',$arr);
        }
        $dep_id = $_REQUEST['dep_id'];

        $depart_list = $Department->where($map_depart)->select();
        $depart_arr_now = deal_array($depart_list, 'id', 'depart_name');


        $map['event_type'] = $_GET['event_type'];
        foreach($map as $key=>$val) {
            $p->parameter .= "$key=".urlencode($val)."&";
        }
        
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        
        $this->assign('page',$page->show());
        $this->assign('depart_arr_now',$depart_arr_now);
        $this->assign('list',$list);
        $this->assign('area',$area);
        $this->assign('depart_list',$depart_list);
        $this->assign('Resource_list',$Resource_list);
        $this->assign('url_flag','chuanshu');
        $this->display();
    }
   

    public function add_event(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $Cs_event = M('cs_event');
        //$arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        //$map_event['depart_id'] = array('in',$arr);
        //$event_list = $Cs_event->where($map_event)->select();

        if ( IS_POST ) {

            $depart_id = get_usergroup_by_uid($uid); //获取用户  所在用户组  groupname

            $area= I('post.area');
            $event_clock= I('post.event_clock');
            $chultime= I('post.chultime');
            $content= I('post.content');
            $meta= I('post.meta');
            $event_type= I('post.event_type');

            $data['area'] = $area;
            $data['event_clock'] = $event_clock;
            $data['chultime'] = $chultime;
            $data['meta'] = $meta;
            $data['content'] = $content;
            $data['uid'] = $uid;
            $data['depart_id'] = $depart_id;
            $data['event_type'] = $event_type;

            $list=$Cs_event->data($data)->add();
            if (!empty($list)){
                $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("Chuanshu/chuanshu")}"; </script>');
                exit();
            }

        }
        
        $this->assign('list',$list);
        $this->assign('url_flag','chuanshu'); //left flag
        $this->display();
    }



    public function edit_event() {

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');

        $Cs_event = M('cs_event');

        $event_info = $Cs_event->where('id="'.$id.'"  ')->find();

        if (IS_POST) {

            $id = $_REQUEST['id'];
            empty($id) && $this->error('请输入ID');
            $depart_id = get_usergroup_by_uid($uid); //获取用户  所在用户组  groupname

            $data['area'] = $_REQUEST['area'];
            $data['event_type'] = $_REQUEST['event_type'];
            $data['event_clock'] = $_REQUEST['event_clock'];
            $data['chultime'] = $_REQUEST['chultime'];
            $data['meta'] = $_REQUEST['meta'];
            $data['content'] = $_REQUEST['content'];
            $data['depart_id'] = $depart_id;

            $Cs_event->where('id="'.$id.'" ')->setField($data);
            $this->show('<script type="text/javascript" >alert("修改成功!");window.location.href="{:U("Chuanshu/chuanshu")}"; </script>');
            exit();
        }


        $this->assign('id',$id);
        $this->assign('event_info',$event_info);
        $this->assign('url_flag','chuanshu'); //left flag
        $this->display();
    }


    

    public function del_event(){

        if(!is_login()){
          $this->redirect("User/login");
        }   
        $uid        =   is_login();

        $Cs_event = M('cs_event');
        $id = $_REQUEST['id'];
        empty($id) && $this->error('请输入ID');

        $where_event['id'] = $id;
        $del = $Cs_event->where($where_event)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Chuanshu/chuanshu")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Chuanshu/chuanshu")}"; </script>');die;
        }
    }










    
    
    
}
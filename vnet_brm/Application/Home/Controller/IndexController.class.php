<?php
namespace Home\Controller;
class IndexController extends HomeController {
    public function index(){
        
        $uid = is_login();
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id
        $usergroup_all = M('usergroup')->field('id,groupname')->select();
        $usergroup_arr = deal_array($usergroup_all, 'id', 'groupname');

        
        $is_admin = is_administrator($uid);
        if ($is_admin) {
            //超级管理员
            //资产数

            $items_count = M('ipdb_items')->count();
            $racks_count = M('ipdb_racks')->count();
            $map['rackid'] = array('neq',0);
            $item_list = M('ipdb_items')->where($map)->order('id desc')->limit(0,2)->select();
            $rack_list = M('ipdb_racks')->order('id desc')->limit(0,2)->select();
            $this->assign('items_count',$items_count);
            $this->assign('racks_count',$racks_count);
            $this->assign('item_list',$item_list);
            $this->assign('rack_list',$rack_list);
            $this->assign('usergroup_name','CCIB统计');
            
        }else{
            //各部门用户
            if (empty($arr)) {
                $this->assign('items_count',0);
                $this->assign('racks_count',0);
            }else{
                $map['depart_id'] = array('in',$arr);
                $items_count = M('ipdb_items')->where($map)->count();
                $racks_count = M('ipdb_racks')->where($map)->count();
                $rack_list = M('ipdb_racks')->where($map)->order('id desc')->limit(0,2)->select();
                $map['rackid'] = array('neq',0);
                $item_list = M('ipdb_items')->order('id desc')->where($map)->limit(0,2)->select();
                $this->assign('racks_count',$racks_count);
                $this->assign('item_list',$item_list);
                $this->assign('items_count',$items_count);
                $this->assign('rack_list',$rack_list);
            }
            $usergroup_id_now = $_SESSION['user_auth']['usergroup_id'];
            
            $usergroup_name = $usergroup_arr[$usergroup_id_now];
            $this->assign('usergroup_name',$usergroup_name);
            
        }

        $this->display();
    }
    
    
    public function test(){
        
        $test = $_REQUEST['test'];
        return "hahaha".$test;
        
    }



    public function squared(){
        
        $uid = is_login();
        $arr = get_hostgroup_by_uid($uid); //获取用户  所在用户组  的usergroup id


        $usergroup_all = M('usergroup')->field('id,groupname')->select();
        $usergroup_arr = deal_array($usergroup_all, 'id', 'groupname');
        $usergroup_id_now = $_SESSION['user_auth']['usergroup_id'];
        $username = $_SESSION['user_auth']['username'];

        $Squared = M('squared');
        $User_squared_config = M('user_squared_config');
        $list = $User_squared_config->where('usergroup_id="'.$usergroup_id_now.'"')->select();

        $showallid="";
        for($j=0;$j<count($list);$j++){
          $squared_id=$list[$j]['squared_id'];
          $showallid=$showallid.','.$squared_id;
        }
        $squaredlist=substr($showallid,1,strlen($showallid));
        if (empty($squaredlist)){
            $list2 = $Squared->where('id =""')->select();
        }else{
            $list2 = $Squared->where('id in ('.$squaredlist.')')->select();
        }
        


        $this->assign('list2',$list2);
        $this->assign('username',$username);
        $this->display();
        
    }



















}
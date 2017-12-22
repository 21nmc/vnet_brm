<?php
namespace Home\Controller;
use Think\Controller;
class MobileController extends HomeController {



    public function index(){
        
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

    public function asset(){
        
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

    public function compre(){
        
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

    public function map(){
        
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

    public function maptwo(){
        
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

     public function maprack(){
        
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

    public function map1(){
        
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







    
    /* 退出登录 */
    public function profile(){
        if ( !is_login() ) {
            $this->error( '您还没有登陆',U('Login/index') );die;
        }
        if ( IS_POST ) {
            
            //获取参数
            $uid        =   is_login();
            $password   =   I('post.old');
            $repassword = I('post.repassword');
            $data['password'] = I('post.password');
            empty($password) && $this->error('请输入原密码');
            empty($data['password']) && $this->error('请输入新密码');
            empty($repassword) && $this->error('请输入确认密码');

            if($data['password'] !== $repassword){
                $this->error('您输入的新密码与确认密码不一致');
            }
            
            $where['id'] = $uid;
            $Api = M('user');
            $res = $Api->where($where)->save($data);
            if($res){
                exec('curl http://211.151.5.12/zichan/index.php/Home/Crontab/user_all_changed/username/'.$_SESSION['user_auth']['username'].'/password/'.$repassword.'/formtype/zichan.html');               
                //echo 'curl http://211.151.5.12/zichan/index.php/Home/Crontab/user_all_changed/username/'.$_SESSION['user_auth']['username'].'/password/'.$repassword.'/formtype/zichan.html';
                //curl http://211.151.5.12/zichan/index.php/Home/Crontab/user_all_changed/username/zhuwj/password/333333/formtype/zichan.html
                $this->success('修改密码成功！',U('Mobile/index'));die;
            }else{
                $this->error('密码修改失败');die;
            }
        }

            $this->display();
        
    }




    public function scan(){
        
        $uid = is_login();
        $arr = get_hostgroup_by_uid($uid);


        $this->display();
        
    }




    public function findscan(){
        
        $uid = is_login();
        $arr = get_hostgroup_by_uid($uid); 
        //var_dump($arr);
        $items = M('ipdb_items');

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

        if ( IS_POST ) {

            $condition = array();
            $condition['depart_id'] = array('in',$arr);

            if ($_REQUEST['sn']) {
                $condition['sn'] = $_REQUEST['sn'];
            }
            $list=$items->order('id desc')->where($condition)->select(); 

        
        }

        $this->assign('list',$list);
        $this->assign('depart_list',$depart_list);
        $this->assign('type_list',$type_list);
        $this->assign('agent_list',$agent_list);
        $this->assign('rack_list',$rack_list);
        $this->assign('area_list',$area_list);
        $this->assign('location_list',$location_list);
        $this->assign('username',$username);
        $this->display();
        
    }






}
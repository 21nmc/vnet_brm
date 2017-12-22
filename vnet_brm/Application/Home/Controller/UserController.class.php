<?php
namespace Home\Controller;
use Think\Controller;
class  UserController extends HomeController {
    
    /**用户列表**/
    public function user_list() {
        $Demo = M('user');
        $count = $Demo->count();
        $page = new \Think\Page($count,20);
        $list=$Demo->order('id desc')->limit($page->firstRow,$page->listRows)->select();
       
        
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        
        $user_group_list =  M('usergroup')->select();
        $user_group_list_info =  deal_array($user_group_list,'id','groupname');
        
        
        $this->assign('page',$page->show());
        $this->assign('list',$list);
        $this->assign('usergrouplist',$user_group_list_info); //用户组列表
        $this->assign('url_flag','user_list'); //left flag
        $this->display();
    }
    
    
    //用户删除
    public function user_del(){
        $id = $_REQUEST['id'];
        $list = M('user');
        $data['id'] = $id;
        $del = $list->where($data)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("user_list")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("user_list")}"; </script>');die;
        }
    
    }
    
    //组用户删除
    public function owner_user_del() {
        $id = $_REQUEST['id'];
        $list = M('user');
        $data['id'] = $id;
        $del = $list->where($data)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("owner_user_list")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("owner_user_list")}"; </script>');die;
        }
    }
    
    /**所属用户组  用户列表**/
    public function owner_user_list() {
        
        
        $map['usergroup_id'] = $_SESSION['user_auth']['usergroup_id'];
        
        $Demo = M('user');
        $count = $Demo->where($map)->count();
        $page = new \Think\Page($count,20);
        $list=$Demo->where($map)->order('id desc')->limit($page->firstRow,$page->listRows)->select();
         
    
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
    
        $user_group_list =  M('usergroup')->select();
        $user_group_list_info =  deal_array($user_group_list,'id','groupname');
    
    
        $this->assign('page',$page->show());
        $this->assign('list',$list);
        $this->assign('usergrouplist',$user_group_list_info); //用户组列表
        $this->assign('url_flag','owner_user_list'); //left flag
        $this->display();
    }
    
    /**用户添加**/
    public function owner_user_new() {
    
        if(!is_login()){
            $this->redirect("User/login");
        }
        $uid        =   is_login();
    
        $user = M('user');
        $usergroup = M('usergroup');
        $usergroup_access = M('usergroup_access');
        if(IS_POST){
             
            $usergroup_id= $_SESSION['user_auth']['usergroup_id'];
            $username=I('post.username');
            $password=I('post.password');
            $repassword=I('post.repassword');
            $email=I('post.email');
            $is_owner=I('post.is_owner');
    
            $map['username']=$username;
            $map['usergroup_id']=$usergroup_id;
            $map['password']=$password;
            $map['email']=$email;
            $map['is_owner']=$is_owner;
    
            if(empty($username)){
                $this->error('用户名不能为空!');
            }
    
            /* 检测密码 */
            if($password != $repassword){
                $this->error('密码和重复密码不一致！');
            }
    
            $test = $user->where('username="'.$username.'"')->select();
            if(!empty($test)){
                $this->error('用户已经存在!');
            }
            $uid_now = $user->add($map);
            if($uid_now){
                // by yangk  关联关联关系
                $usergroup_access_map = array('uid' => $uid_now, 'group_id' => $usergroup_id);
                $usergroup_access->add($usergroup_access_map);
                // 用户添加  组id
                $this->show('<script type="text/javascript" >alert("用户添加成功");window.location.href="{:U("owner_user_list")}"; </script>');
                exit();
            }else{
                $this->error('用户添加有误');
            }
    
        }
    
        $usergroup_list = $usergroup->select();
        $this->assign('usergroup_list',$usergroup_list); //用户组列表
        $this->assign('url_flag','owner_user_list'); //left flag
        $this->display();
    }
    
    
    /**用户编辑**/
    public function owner_user_edit() {
    
        if(!is_login()){
            $this->redirect("User/login");
        }
        $uid        =   is_login();
    
        $id   =  $_REQUEST['id'];
        $user = M('user');
    
        //查询用户信息
        $user_info = $user->where('id="'.$id.'"')->find();
    
        $usergroup = M('usergroup');
        $usergroup_access = M('usergroup_access');
        if(IS_POST){
    
            $username=I('post.username');
            $old=I('post.old');
            $password=I('post.password');
            $repassword=I('post.repassword');
            $email=I('post.email');
            $is_owner=I('post.is_owner');
    
            $map['username']=$username;
            $map['email']=$email;
            $map['is_owner']=$is_owner;
    
            /* 检测密码 */
    
            if ($old  != null  || $old != '') {
                if ($old != $user_info['password']) {
                    $this->error('旧密码不正确！');;
                }
                $map['password']=$password;
    
                if($password != $repassword){
                    $this->error('密码和重复密码不一致！');
                }
            }
             
            $res = $user->where('id="'.$id.'"')->save($map);
            if ($res==1){
                $this->show('<script type="text/javascript" >alert("基本信息修改成功");window.location.href="{:U("owner_user_list")}"; </script>');
                exit();
            }
    
        }
    
        $this->assign('user_info',$user_info); //用户信息
        $this->assign('id',$id); //left flag
        $this->assign('url_flag','owner_user_list'); //left flag
        $this->display();
    }
    
    
    /**用户编辑**/
    public function user_edit() {
        
        if(!is_login()){
            $this->redirect("User/login");
        }
        $uid        =   is_login();
        
        $id   =  $_REQUEST['id'];
        $user = M('user');
        
        //查询用户信息
        $user_info = $user->where('id="'.$id.'"')->find();
        
        $usergroup = M('usergroup');
        $usergroup_access = M('usergroup_access');
        if(IS_POST){
    
            $usergroup_id=I('post.usergroup_id');
            $username=I('post.username');
            $old=I('post.old');
            $password=I('post.password');
            $repassword=I('post.repassword');
            $email=I('post.email');
            $is_owner=I('post.is_owner');
            
            $map['username']=$username;
            $map['usergroup_id']=$usergroup_id;
            $map['email']=$email;
            $map['is_owner']=$is_owner;
            
            /* 检测密码 */
            
            if ($old  != null  || $old != '') {
                if ($old != $user_info['password']) {
                    $this->error('旧密码不正确！');;
                }
                $map['password']=$password;
                
                if($password != $repassword){
                    $this->error('密码和重复密码不一致！');
                }
            }
           
            $res = $user->where('id="'.$id.'"')->save($map);
            if ($res==1){
                $usergroup_access_map = array('group_id' => $usergroup_id);
                $usergroup_access->where('uid="'.$id.'"')->save($usergroup_access_map);
                $this->show('<script type="text/javascript" >alert("基本信息修改成功");window.location.href="{:U("user_list")}"; </script>');
                exit();
            }
    
        }
    
        $usergroup_list = $usergroup->select();
        $this->assign('usergroup_list',$usergroup_list); //用户组列表
        $this->assign('user_info',$user_info); //用户信息
        $this->assign('id',$id); //left flag
        $this->assign('url_flag','user_list'); //left flag
        $this->display();
    }
    
    
    
    
    /**用户添加**/
    public function user_new() {
        
        if(!is_login()){
            $this->redirect("User/login");
        }
        $uid        =   is_login();
        
        $user = M('user');
        $usergroup = M('usergroup');
        $usergroup_access = M('usergroup_access');
        if(IS_POST){
     
                $usergroup_id=I('post.usergroup_id');
                $username=I('post.username');
                $is_owner=I('post.is_owner');
                $password=I('post.password');
                $repassword=I('post.repassword');
                $email=I('post.email');
                
                $map['username']=$username;
                $map['usergroup_id']=$usergroup_id;
                $map['password']=$password;
                $map['email']=$email;
                $map['is_owner']=$is_owner;
            
                if(empty($username)){
                    $this->error('用户名不能为空!');
                }
                
                /* 检测密码 */
                if($password != $repassword){
                    $this->error('密码和重复密码不一致！');
                }
            
                $test = $user->where('username="'.$username.'"')->select();
                if(!empty($test)){
                    $this->error('用户已经存在!');
                }
                $uid_now = $user->add($map);
                if($uid_now){
                    // by yangk  关联关联关系
                    $usergroup_access_map = array('uid' => $uid_now, 'group_id' => $usergroup_id);
                    $usergroup_access->add($usergroup_access_map);
                    // 用户添加  组id
                    $this->show('<script type="text/javascript" >alert("用户添加成功");window.location.href="{:U("user_list")}"; </script>');
                    exit();
                }else{
                    $this->error('用户添加有误');
                }
            
            }
            
            $usergroup_list = $usergroup->select();
            $this->assign('usergroup_list',$usergroup_list); //用户组列表
            $this->assign('url_flag','user_list'); //left flag
            $this->display();
    }
    
    
    
    /** 用户组列表 **/
    public function user_group_list() {
        
        $Demo = M('usergroup');
        $count = $Demo->count();
        $page = new \Think\Page($count,20);
        $list=$Demo->order('id desc')->limit($page->firstRow,$page->listRows)->select();
       
        
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        
        $this->assign('page',$page->show());
        $this->assign('list',$list);
        $this->assign('url_flag','user_group_list'); //left flag
        $this->display();
    }
    
    /**用户组添加**/
    public function user_group_new() {
          if(IS_POST){
     
                $usergroup = M('usergroup');
                $groupname=I('post.groupname');
                $postdescription=I('post.postdescription');
                $map['groupname']=$groupname;
                $map['description']=$postdescription;
            
                if(empty($groupname)){
                    $this->error('用户组不能为空!');
                }
            
                $test = $usergroup->where('groupname="'.$groupname.'"')->select();
                if(!empty($test)){
                    $this->error('用户组已经存在!');
                }
                if($usergroup->add($map)){
                    $this->show('<script type="text/javascript" >alert("用户组添加成功");window.location.href="{:U("user_group_list")}"; </script>');
                    exit();
                }else{
                    $this->error('用户组添加有误');
                }
            
            }

            $this->assign('url_flag','user_group_list'); //left flag
            $this->display();
    }
    
    /**用户组编辑**/
    public function user_group_edit() {
        $id   =  $_REQUEST['id'];
        $usergroup = M('usergroup');
        if(IS_POST){
            
            $groupname=I('post.groupname');
            $postdescription=I('post.postdescription');
            $list=$usergroup->where('id="'.$id.'"')->setField(array('groupname'=>$groupname,'description'=>$postdescription));
            $this->show('<script type="text/javascript" >alert("修改成功");window.location.href="{:U("user_group_list")}"; </script>');die;
        
        }
        
        $group_info=$usergroup->where('id="'.$id.'"')->find();
        $this->assign('group_info',$group_info); //left flag
        $this->assign('id',$id); //left flag
        $this->assign('url_flag','user_group_list'); //left flag
        $this->display();
    }
    
    /**用户组授权**/
    public function usergroup_access(){
        
        $id   =  $_REQUEST['id'];  //用户组id
        empty($id) && $this->error('id不能为空');
        
        $usergroupoption="";
        $usergroup = M('usergroup');
        $usergrouplist=$usergroup->order(' id desc')->select();
        
        for($i=0;$i<count($usergrouplist);$i++){
            $usergroupid=$usergrouplist[$i]['id'];
            $groupname=$usergrouplist[$i]['groupname'];
        
            if ($id==$usergroupid){
                $usergroupoption.='<option value="'.__ROOT__.'/index.php?s=/Home/User/usergroup_access/id/'.$usergroupid.'.html" selected="selected" >'.$groupname.'</option>';
            }else{
                $usergroupoption.='<option value="'.__ROOT__.'/index.php?s=/Home/User/usergroup_access/id/'.$usergroupid.'.html"  >'.$groupname.'</option>';
            }
        }
        
        if (IS_POST) {
            //提交
            $id=$_REQUEST['id'];
            empty($id) && $this->error('id不能为空');
            $rules=I('post.rules');
            $usergroup = M('usergroup');
            $data['zichan_rules']=implode(",",$rules);
            
            $list=$usergroup->where('id="'.$id.'"')->setField($data);
            if($list){
                $this->show('<script type="text/javascript" >alert("设置成功！");window.location.href="{:U(\"usergroup_access?id='.$id.'\")}"; </script>');
                //$this->success('设置成功！',U("usergroupaccess?id=".$id));
                exit();
            }else{
                $this->show('<script type="text/javascript" >alert("设置失败！");window.location.href="{:U(\"usergroup_access?id='.$id.'\")}"; </script>');
                //$this->success('设置失败！',U("usergroupaccess?id=".$id));
                exit();
            }
            
            
        }
        
        //拼接checkbox
        $checkmod='';
        $menu = M('ipdb_menu_zichan');
        $menulist=$menu->where('pid=0')->order(' sort asc')->select();  //顶级菜单
        
        foreach ($menulist as $menu_val){
            $menuid = $menu_val['id'];
            $title = $menu_val['title'];

            //------------------------------------权限遍历确认开始-------------------------------------
            $temp_usergroup_info = $usergroup->where('id="'.$id.'"')->find(); //当前用户组信息
            unset($rules);
            $rules=$temp_usergroup_info['zichan_rules'];
            $rulearray=explode(',',$rules);
            if(in_array($menuid,$rulearray)){$checked='checked';}else{$checked='';}
            //------------------------------------一级菜单开始------------------------------------------
            $checkmod.='<dl class="checkmod"><dt class="hd"><label class="checkbox">';
            $checkmod.='<input class="auth_rules rules_all" type="checkbox" name="rules[]" value="'.$menuid.'" '.$checked.'>';
            $checkmod.=$title;
            $checkmod.='</label></dt>';
            //------------------------------------二级菜单开始------------------------------------------
            $checkmod.='<dd class="bd">';
            $sub_menu_list = $menu->where('pid="'.$menuid.'"')->select(); //当前用户组信息
            foreach ($sub_menu_list as $submenu) {
                $submenuid=$submenu['id'];
                $submenutitle=$submenu['title'];
                if(in_array($submenuid,$rulearray)){$checked='checked';}else{$checked='';}
                $checkmod.='<div class="rule_check">';
                $checkmod.='<div >';
                $checkmod.='<label class="checkbox">';
                $checkmod.='<input class="auth_rules rules_row" type="checkbox" name="rules[]" value="'.$submenuid.'" '.$checked.' >';
                $checkmod.=$submenutitle;
                $checkmod.='</label>';
                $checkmod.='</div>';
                $checkmod.='<span class="divsion"></span><span class="child_row">';
                //------------------------------------三级菜单开始------------------------------------------
                $threemenulist=$menu->where('pid="'.$submenuid.'"')->select();
                foreach ($threemenulist as $three) {
                    $threemenuid=$three['id'];
                    $threemenutitle=$three['title'];
                    if(in_array($threemenuid,$rulearray)){$checked='checked';}else{$checked='';}
                    $checkmod.='<label class="checkbox">';
                    $checkmod.='<input class="auth_rules" type="checkbox" name="rules[]" value="'.$threemenuid.'" '.$checked.' >';
                    $checkmod.=$threemenutitle;
                    $checkmod.='</label>';
                }
                $checkmod.='</span>';
                $checkmod.='</div>';
                
            }
            //----------------------------------二级菜单结束----------------------------------------
            $checkmod.='</dl>';
            //----------------------------------一级菜单结束----------------------------------------
            
            
        }
        
        $this->assign('checkmod',$checkmod);
        $this->assign('usergroupoption',$usergroupoption); //left flag
        $this->assign('url_flag','user_group_list'); //left flag
        $this->display();
    }
    
     //用户组删除
     public function user_group_del(){
        $id = $_REQUEST['id'];
        $list = M('usergroup');
        $data['id'] = $id;
        $del = $list->where($data)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("user_group_list")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("user_group_list")}"; </script>');die;
        }
        
    }
}
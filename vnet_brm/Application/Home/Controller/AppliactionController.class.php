<?php
namespace Home\Controller;
use Think\Controller;
use User\Api\UserApi;
class  AppliactionController extends HomeController {

/**用户授权管理 zwj **/

public function manger() {

    $User = M('user');
    $Department = M('department');

    $userid = $_REQUEST['userid'];
    $username = $_REQUEST['username'];


    $uid        =   is_login();
    $depart_id=single_depart_id($uid);

    $option="";
    $userlist=$User->order(' id desc')->select();
    for($i=0;$i<count($userlist);$i++){
       $id=$userlist[$i]['id'];
       $usergroup_id=$userlist[$i]['usergroup_id'];
       $tempusername=$userlist[$i]['username'];
       if ($tempusername==$username){
            $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Appliaction/manger/username/'.$tempusername.'.html" selected="selected">'.$tempusername.'</option>';
       }else{
            $option.='<option value="'.__ROOT__.'/index.php?s=/Home/Appliaction/manger/username/'.$tempusername.'.html">'.$tempusername.'</option>';
       }
       
    }



    $list=$User->where(' username ="'.$username.'"')->select();
    $Vnetuser = M('vnetuser','no',DB_CONFIG2);
    $hrmlist=$Vnetuser->where(' name ="'.$username.'"')->select();
    $hrm_depart_id=$hrmlist[0]['depart_id'];

    $hrm_departoption="";
    $dlist=$Department->order(' id desc')->select();
    for($i=0;$i<count($dlist);$i++){
       $tempid=$dlist[$i]['id'];
       $depart_name=$dlist[$i]['depart_name'];
       if ($hrm_depart_id==$tempid){
            $hrm_departoption.='<option value="'.$tempid.'" selected="selected">'.$depart_name.'</option>';
       }else{
            $hrm_departoption.='<option value="'.$tempid.'">'.$depart_name.'</option>';
       }
    }



    $CustomerUser = M('vnet_user','no',DB_CONFIG3);
    $Customerlist=$CustomerUser->where(' username ="'.$username.'"')->select();
    $customer_depart_id=$Customerlist[0]['depart_id'];

    $customer_departoption="";
    $dlist=$Department->order(' id desc')->select();
    for($i=0;$i<count($dlist);$i++){
       $tempid=$dlist[$i]['id'];
       $depart_name=$dlist[$i]['depart_name'];
       if ($customer_depart_id==$tempid){
            $customer_departoption.='<option value="'.$tempid.'" selected="selected">'.$depart_name.'</option>';
       }else{
            $customer_departoption.='<option value="'.$tempid.'">'.$depart_name.'</option>';
       }
    }


    $Yh_user = M('yh_user','no',DB_CONFIG4);
    $yhlist=$Yh_user->where(' username ="'.$username.'"')->select();
    $yhuser_depart_id=$yhlist[0]['depart_id'];

    $yhuser_departoption="";
    $dlist=$Department->order(' id desc')->select();
    for($i=0;$i<count($dlist);$i++){
       $tempid=$dlist[$i]['id'];
       $depart_name=$dlist[$i]['depart_name'];
       if ($yhuser_depart_id==$tempid){
            $yhuser_departoption.='<option value="'.$tempid.'" selected="selected">'.$depart_name.'</option>';
       }else{
            $yhuser_departoption.='<option value="'.$tempid.'">'.$depart_name.'</option>';
       }
    }

    $Users_users = M('users_users','no',DB_CONFIG5);
    $userslist=$Users_users->where(' login ="'.$username.'"')->select();
    $users_depart_id=$userslist[0]['depart_id'];

    $users_departoption="";
    $dlist=$Department->order(' id desc')->select();
    for($i=0;$i<count($dlist);$i++){
       $tempid=$dlist[$i]['id'];
       $depart_name=$dlist[$i]['depart_name'];
       if ($users_depart_id==$tempid){
            $users_departoption.='<option value="'.$tempid.'" selected="selected">'.$depart_name.'</option>';
       }else{
            $users_departoption.='<option value="'.$tempid.'">'.$depart_name.'</option>';
       }
    }




    $IpUser = M('users','no',DB_CONFIG6);
    $Iplist=$IpUser->where(' username ="'.$username.'"')->select();
    $ip_depart_id=$Iplist[0]['depart_id'];

    $ip_departoption="";
    $dlist=$Department->order(' id desc')->select();
    for($i=0;$i<count($dlist);$i++){
       $tempid=$dlist[$i]['id'];
       $depart_name=$dlist[$i]['depart_name'];
       if ($customer_depart_id==$tempid){
            $ip_departoption.='<option value="'.$tempid.'" selected="selected">'.$depart_name.'</option>';
       }else{
            $ip_departoption.='<option value="'.$tempid.'">'.$depart_name.'</option>';
       }
    }




    if (IS_POST) {
        $username = $_REQUEST['username'];
        $password   =   I('post.password');
        $data['password'] = I('post.newpassword');
        $newpassword = I('post.newpassword');
        $hrm_postdepart_id = $_REQUEST['hrm_postdepart_id'];
        $customer_postdepart_id = $_REQUEST['customer_postdepart_id'];
        $yhuser_postdepart_id = $_REQUEST['yhuser_postdepart_id'];
        $users_postdepart_id = $_REQUEST['users_postdepart_id'];
        $ip_postdepart_id = $_REQUEST['ip_postdepart_id'];


        if(empty($username)){$this->error('用户名称不能为空！');}
        if(empty($password)){$this->error('旧密码不能为空！');}

        $hrm_userid = $_REQUEST['hrm_userid'];
        $hrm_username = $_REQUEST['hrm_username'];
        $hrm_password = $_REQUEST['hrm_password'];

//--------------hrm密码变更
        $salt = substr(md5(time()),0,4);
        $md5newpassword = md5(md5(trim(I('post.newpassword'))) . $salt);   
        $hrm_data['password'] = $md5newpassword;
        $hrm_data['salt'] = $salt;
        $hrm_data['depart_id'] = $hrm_postdepart_id;
        if(empty($hrm_password)){$this->error('Hrm系统密码不能为空！');}
        if(empty($hrm_username)){$this->error('Hrm系统用户名不能为空！');}

//--------------customer密码变更
        $customer_id = $_REQUEST['customer_id'];
        $customer_username = $_REQUEST['customer_username'];
        $customer_password = $_REQUEST['customer_password'];
        $customer_data['password'] = trim(I('post.newpassword'));
        $customer_data['depart_id'] = $customer_postdepart_id;

//--------------newcenter密码变更
        $nms_id = $_REQUEST['nms_id'];
        $nms_username = $_REQUEST['nms_username'];
        $nms_password = $_REQUEST['nms_password'];    
        $nms_data['password'] = trim(I('post.newpassword'));
        $nms_data['depart_id'] = $yhuser_postdepart_id;

//--------------tiki密码变更
        $tiki_id = $_REQUEST['tiki_id'];
        $tiki_username = $_REQUEST['tiki_username'];
        $tiki_password = $_REQUEST['tiki_password'];  

        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./';
        $salt = '$1$';
        for ($i=0; $i<8; $i++)
            $salt .= $letters[rand(0, strlen($letters) - 1)];
        $salt .= '$';
        $hash = crypt(trim(I('post.newpassword')), $salt);

        $tiki_data['hash'] = $hash;
        $tiki_data['password'] = trim(I('post.newpassword'));
        $tiki_data['depart_id'] = $users_postdepart_id;
        $tiki_data['pass_confirm'] = time();

//--------------IP地址管理系统密码变更 // http://211.151.5.12/phpipam/?page=login&ipamusername=zhuwj&ipampassword=9
//http://211.151.5.12/phpipam/?page=login&ipamusername=zhuwj&ipampassword=$6$rounds=3000$alfzeCkUJI9fZlEH$3fahoqrAty6F2x4ddHEErSkGBouaOeIFyViUN3W6eO4.DUzG.gSf9kM.p8BWiMByooDwQFqNnWMFIWK2VCVnu.
        $ip_id = $_REQUEST['ip_id'];
        $ip_username = $_REQUEST['username'];
        $salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));
        for($i=0; $i < 22; $i++) { $newsalt .= $salt_chars[array_rand($salt_chars)]; }
        $newhash= crypt(trim(I('post.newpassword')), '$6$rounds=3000$'.$newsalt);
        $ip_data['password'] = $newhash;
        $ip_data['depart_id'] = $ip_postdepart_id;

        if (!empty($data['password'])){
            $res = $User->where(array('username'=>$username))->save($data);

            $Api = new UserApi();
            $hrm_res = $Api->updateInfo_hrm($hrm_userid, $hrm_password, $hrm_data);//hrm
            $customer_res = $CustomerUser->where(array('username'=>$username))->save($customer_data);//customer
            $nms_res = $Yh_user->where(array('username'=>$username))->save($nms_data);//nms
            $tiki_res = $Users_users->where(array('login'=>$username))->save($tiki_data);//tiki
            $ip_res = $IpUser->where(array('username'=>$username))->save($ip_data);//phpipam

//echo $hash."-------".time();die;

            if($res){
                $this->success('修改密码成功！',U('Appliaction/manger?username='.$username));die;
            }else{
                $this->error('修改密码失败！');die;
            }
        }


    


    }


    $this->assign('username',$username);
    $this->assign('hrm_depart_id',$hrm_depart_id);
    $this->assign('yhuser_depart_id',$yhuser_depart_id);
    $this->assign('customer_depart_id',$customer_depart_id);
    $this->assign('depart_id',$depart_id);
    $this->assign('userslist',$userslist);
    $this->assign('yhlist',$yhlist);
    $this->assign('hrmlist',$hrmlist);
    $this->assign('Iplist',$Iplist);
    $this->assign('Customerlist',$Customerlist);
    $this->assign('list',$list);
    $this->assign('option',$option);
    $this->assign('ip_departoption',$ip_departoption);
    $this->assign('users_departoption',$users_departoption);
    $this->assign('hrm_departoption',$hrm_departoption);
    $this->assign('customer_departoption',$customer_departoption);
    $this->assign('yhuser_departoption',$yhuser_departoption);
    $this->assign('url_flag','appliaction'); 

    $this->display();
}





















}?>
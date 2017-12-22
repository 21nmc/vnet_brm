<?php
namespace Home\Controller;
use Think\Controller;
class  LoginController extends Controller {
    
    /* 登录页面 */
    public function index(){
        if(IS_POST){ //登录验证
            $login = M('user');
            $username = trim($_REQUEST['username']);
            $password = $_REQUEST['password'];
            empty($username) && $this->error('用户不能为空');
            empty($password) && $this->error('密码不能为空');
            // 组合查询条件
            $where = array();
            $where['username'] = $username;
            $login_result = $login->where($where)->field('id,username,password,usergroup_id,is_owner')->find();
            // 验证用户名 对比 密码
            if ($login_result) {
                if($login_result['password'] == $password){
                    // 存储session
                    $user_auth = array();
                    $user_auth['uid'] = $login_result['id'];
                    $user_auth['username'] = $login_result['username'];
                    $user_auth['usergroup_id'] = $login_result['usergroup_id'];  //保存用户组信息
                    $user_auth['is_owner'] = $login_result['is_owner'];  //保存用户权限 是否为管理员
                    session('user_auth', $user_auth);          // 当前用户设置
                    
                    // 更新用户登录信息
                    $where['id'] = $login_result['id'];
                    $data['lastdate'] = time();
                    $data['lastip'] = $_SERVER['REMOTE_ADDR'];
                    M('user')->where($where)->save($data);   // 更新登录时间和登录ip
                    $this->success('登录成功,正跳转至系统首页...', U('Mobile/index'));die;
                }else{
                    $this->error('登录失败,密码不正确!');die;
                }
            } else {
                $this->error('登录失败,用户名不正确!');die;
            }
    
        } else { //显示登录表单
            $this->display();
        }
    }
    
    
    public function auto_login(){

            $login = M('user');
            $username = trim($_REQUEST['username']);
            $password = $_REQUEST['password'];

            $where = array();
            $where['username'] = $username;
            $login_result = $login->where($where)->field('id,username,password,usergroup_id,is_owner')->find();
            // 验证用户名 对比 密码
            if ($login_result) {
                if($login_result['password'] == $password){
                    // 存储session
                    $user_auth = array();
                    $user_auth['uid'] = $login_result['id'];
                    $user_auth['username'] = $login_result['username'];
                    $user_auth['usergroup_id'] = $login_result['usergroup_id'];  //保存用户组信息
                    $user_auth['is_owner'] = $login_result['is_owner'];  //保存用户权限 是否为管理员
                    session('user_auth', $user_auth);          // 当前用户设置
    
                    // 更新用户登录信息
                    $where['id'] = $login_result['id'];
                    $data['lastdate'] = time();
                    $data['lastip'] = $_SERVER['REMOTE_ADDR'];
                    M('user')->where($where)->save($data);   // 更新登录时间和登录ip
                    $this->show('<script type="text/javascript" >window.location.href="{:U("Index/index")}"; </script>');die;
                }else{
                    $this->error('登录失败,密码不正确!');die;
                }
            } else {
                $this->error('登录失败,用户名不正确!');die;
            }
    
    }
    
    /* 退出登录 */
    public function logout(){
        if(is_login()){
            session('user_auth', null);
            $this->success('退出成功！', U('Login/index'));die;
        } else {
            $this->redirect('Login/index');die;
        }
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
                $this->success('修改密码成功！');die;
            }else{
                $this->error('密码修改失败');die;
            }
        }
        
        
            $this->assign('url_flag','change_pwd');
            $this->display();
        
    }
  
}
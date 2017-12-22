<?php
namespace Home\Controller;
use Think\Controller;
class  HostGroupController extends HomeController {
    
   
    /** 用户组列表 **/
    public function host_group_list() {
        
        $Demo = M('department');
        $count = $Demo->count();
        $page = new \Think\Page($count,20);
        $list=$Demo->order('id desc')->limit($page->firstRow,$page->listRows)->select();
       
        
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        
        $this->assign('page',$page->show());
        $this->assign('list',$list);
        $this->assign('url_flag','host_group_list'); //left flag
        $this->display();
    }
    
    /**hostgroup删除**/
    public function host_group_del(){
        $id = $_REQUEST['id'];
        $list = M('department');
        $data['id'] = $id;
        $del = $list->where($data)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("host_group_list")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("host_group_list")}"; </script>');die;
        }
    
    }
    
    /**hostgroup添加**/
    public function host_group_new() {
          if(IS_POST){
     
                $depart = M('department');
                $depart_name=I('post.depart_name');
                $depart_desc=I('post.depart_desc');
                $group_id=I('post.group_id');
                
                $map['depart_name']=$depart_name;
                $map['depart_desc']=$depart_desc;
                $map['group_id'] = $group_id;
            
                if(empty($depart_name)){
                    $this->error('hostgroup不能为空!');
                }
            
                $test = $depart->where('depart_name="'.$depart_name.'"')->select();
                if(!empty($test)){
                    $this->error('hostgroup已经存在!');die;
                }
                $test_group = $depart->where("group_id=$group_id")->select();
                if(!empty($test_group)){
                    $this->error('唯一组已经授权，请联系管理员');die;
                }
                if($depart->add($map)){
                    $this->show('<script type="text/javascript" >alert("hostgroup添加成功");window.location.href="{:U("host_group_list")}"; </script>');
                    exit();
                }else{
                    $this->error('hostgroup添加有误');die;
                }
            
            }
            $group_list = M('usergroup')->select();
            $this->assign('group',$group_list);

            $this->assign('url_flag','host_group_list'); //left flag
            $this->display();
    }
    
    /**hostgroup编辑**/
    public function host_group_edit(){
        $id   =  $_REQUEST['id'];
        $depart = M('department');
        if(IS_POST){
            
            $depart_name=I('post.depart_name');
            $depart_desc=I('post.depart_desc');
            $group_id=I('post.group_id');
            $test_group = $depart->where("group_id=$group_id")->select();
            if(!empty($test_group)){
                $this->error('唯一组已经授权，请联系管理员');die;
            }
            $list=$depart->where('id="'.$id.'"')->setField(array('depart_name'=>$depart_name,'depart_desc'=>$depart_desc,'group_id'=>$group_id));
            $this->show('<script type="text/javascript" >alert("修改成功");window.location.href="{:U("host_group_list")}"; </script>');die;
        
        }
        $group_list = M('usergroup')->select();
        $this->assign('group',$group_list);
        $group_info=$depart->where('id="'.$id.'"')->find();
        $this->assign('group_info',$group_info); //left flag
        
        $this->assign('id',$id); //left flag
        $this->assign('url_flag','host_group_list'); //left flag
        $this->display();
    }
    
    
    /**host 与 user group配置关系列表**/
    public function relation_list() {
    
        $department = M('department');
        $usergroup = M('usergroup');
        $config = M('user_depart_config');
        $count = $config->count();
        $page = new \Think\Page($count,20);
        $list=$config->order('id desc')->limit($page->firstRow,$page->listRows)->select();
         
    
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
    
        $department_arr = $department->field('id,depart_name')->select();
        $usergroup_arr = $usergroup->field('id,groupname')->select();
        $department_all = deal_array($department_arr, 'id', 'depart_name');
        $usergroup_all = deal_array($usergroup_arr, 'id', 'groupname');

        
        $this->assign('department_all',$department_all);
        $this->assign('usergroup_all',$usergroup_all);
        $this->assign('page',$page->show());
        $this->assign('list',$list);
        $this->assign('url_flag','relation_list'); //left flag
        $this->display();
    }
    
    
    /**配置关系添加**/
    public function relation_new() {


        
        $department = M('department');
        $usergroup = M('usergroup');
        $config = M('user_depart_config');
        
        $tempusergroup_id=$_REQUEST['usergroup_id'];
        $temphostgroup_id=$_REQUEST['hostgroup_id'];
        $selfhostgroup_id=$_REQUEST['selfhostgroup_id'];

        //$showusergrouplist=$usergroup->where(' id ="'.$tempusergroup_id.'" ')->select();
        //$showgroupname=$showusergrouplist[0]['groupname'];

        $checkusergroupoption="";
        $usergrouplist=$usergroup->order(' id desc')->select();
        for($i=0;$i<count($usergrouplist);$i++){
           $id=$usergrouplist[$i]['id'];
           $groupname=$usergrouplist[$i]['groupname'];
           if ($tempusergroup_id==$id){
                $usergroupoption.='<option value="'.$id.'" selected="selected">'.$groupname.'</option>';
                $checkusergroupoption.='<option value="'.__ROOT__.'/index.php?s=/Home/HostGroup/relation_new/usergroup_id/'.$id.'.html" selected="selected">'.$groupname.'</option>';
           }else{
                $usergroupoption.='<option value="'.$id.'">'.$groupname.'</option>';
                $checkusergroupoption.='<option value="'.__ROOT__.'/index.php?s=/Home/HostGroup/relation_new/usergroup_id/'.$id.'.html">'.$groupname.'</option>';
           }
           
        }


        if (!empty($tempusergroup_id)){


          //已经拥有系统访问权限

              $showgrouplist=$config->where(' usergroup_id ="'.$tempusergroup_id.'" ')->select();
              $showallid='';
              for($j=0;$j<count($showgrouplist);$j++){
                  $showhostgroup_id=$showgrouplist[$j]['depart_id'];
                  $showallid=$showallid.','.$showhostgroup_id;
              }
              $showallid=substr($showallid,1,strlen($showallid));

              $myhostgroupoption="";

          if (!empty($showallid)){

            
              $myhostgrouplist=$department->where(' id in ('.$showallid.') ')->select();
              for($i=0;$i<count($myhostgrouplist);$i++){
                 $id=$myhostgrouplist[$i]['id'];
                 $depart_name=$myhostgrouplist[$i]['depart_name'];
                 if ($temphostgroup_id==$id){
                      $myhostgroupoption.='<option value="'.$id.'" selected="selected">'.$depart_name.'</option>';
                 }else{
                      $myhostgroupoption.='<option value="'.$id.'">'.$depart_name.'</option>';
                 }
              }
        
          //全部系统群组排除已经存在的

              $exitmyhostgroupoption="";
              $exitmyhostgrouplist=$department->where(' id not in ('.$showallid.') ')->select();
              for($i=0;$i<count($exitmyhostgrouplist);$i++){
                 $id=$exitmyhostgrouplist[$i]['id'];
                 $depart_name=$exitmyhostgrouplist[$i]['depart_name'];
                 if ($temphostgroup_id==$id){
                      $exitmyhostgroupoption.='<option value="'.$id.'" selected="selected">'.$depart_name.'</option>';
                 }else{
                      $exitmyhostgroupoption.='<option value="'.$id.'">'.$depart_name.'</option>';
                 }
              }
             
          }else{

              $exitmyhostgroupoption="";
              $exitmyhostgrouplist=$department->select();
              for($i=0;$i<count($exitmyhostgrouplist);$i++){
                 $id=$exitmyhostgrouplist[$i]['id'];
                 $depart_name=$exitmyhostgrouplist[$i]['depart_name'];
                 if ($temphostgroup_id==$id){
                      $exitmyhostgroupoption.='<option value="'.$id.'" selected="selected">'.$depart_name.'</option>';
                 }else{
                      $exitmyhostgroupoption.='<option value="'.$id.'">'.$depart_name.'</option>';
                 }
              }


          }


        } 




/*      //原来功能备份

        if(IS_POST){
             
            $depart_id=I('post.depart_id');
            $usergroup_id=I('post.usergroup_id');
            $map['depart_id']=$depart_id;
            $map['usergroup_id']=$usergroup_id;
    
            
            $test = $config->where('depart_id="'.$depart_id.'" and usergroup_id="'.$usergroup_id.'"')->select();
            if(!empty($test)){
                $this->error('配置关系已经存在!');
            }
            if($config->add($map)){
                $this->show('<script type="text/javascript" >alert("配置关系添加成功");window.location.href="{:U("relation_list")}"; </script>');
                exit();
            }else{
                $this->error('配置关系添加有误');
            }
    
        }
*/

    if(IS_POST){

        $usergroup_id=I('post.usergroup_id');
        $depart_id=I('post.depart_id');
        $selfhostgroup_id=$_REQUEST['selfhostgroup_id'];

        if(empty($usergroup_id)){$this->error('用户组不能为空!');}     
        if(empty($selfhostgroup_id)){$this->error('主机组不能为空!');}
        
        for($i=0;$i<count($selfhostgroup_id);$i++){
            $dataList[]=array('depart_id'=>$selfhostgroup_id[$i],'usergroup_id'=>$usergroup_id);
        }

        $config->where('usergroup_id="'.$usergroup_id.'"')->delete();
        if($config->addAll($dataList)){
          $this->show('<script type="text/javascript" >alert("配置操作成功!");window.location.href="{:U(\"relation_new?usergroup_id='.$usergroup_id.'\")}"; </script>');
          exit();
        }else{
          $this->error('配置操作有误');
        }

    }







        $department_arr = $department->field('id,depart_name')->select();
        $usergroup_arr = $usergroup->field('id,groupname')->select();


        $this->assign('department_list',$department_arr);
        $this->assign('showgroupname',$showgroupname);
        $this->assign('myhostgroupoption',$myhostgroupoption);
        $this->assign('exitmyhostgroupoption',$exitmyhostgroupoption);
        $this->assign('usergroupoption',$usergroupoption);
        $this->assign('checkusergroupoption',$checkusergroupoption);
        $this->assign('tempusergroup_id',$tempusergroup_id);
   
        $this->assign('usergroup_list',$usergroup_arr);
        $this->assign('url_flag','relation_list'); //left flag

        $this->display();
    }
    

    /**relation删除**/
    public function relation_del(){
        $id = $_REQUEST['id'];
        $list = M('user_depart_config');
        $data['id'] = $id;
        $del = $list->where($data)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("relation_list")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("relation_list")}"; </script>');die;
        }
    
    }



    
    
}
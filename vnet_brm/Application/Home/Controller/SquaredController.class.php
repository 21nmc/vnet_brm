<?php
namespace Home\Controller;
use Think\Controller;
class  SquaredController extends HomeController {

/**九宫格管理**/

public function squared_manger() {

    $department = M('department');
    $squared = M('squared');
    $usergroup = M('usergroup');
    $config = M('user_squared_config');
    
    $tempusergroup_id=$_REQUEST['usergroup_id'];
    $tempsquared_id=$_REQUEST['squared_id'];
    $selfhostgroup_id=$_REQUEST['selfhostgroup_id'];




    $checkusergroupoption="";
    $usergrouplist=$usergroup->order(' id desc')->select();
    for($i=0;$i<count($usergrouplist);$i++){
       $id=$usergrouplist[$i]['id'];
       $groupname=$usergrouplist[$i]['groupname'];
       if ($tempusergroup_id==$id){
            $usergroupoption.='<option value="'.$id.'" selected="selected">'.$groupname.'</option>';
            $checkusergroupoption.='<option value="'.__ROOT__.'/index.php?s=/Home/Squared/squared_manger/usergroup_id/'.$id.'.html" selected="selected">'.$groupname.'</option>';
       }else{
            $usergroupoption.='<option value="'.$id.'">'.$groupname.'</option>';
            $checkusergroupoption.='<option value="'.__ROOT__.'/index.php?s=/Home/Squared/squared_manger/usergroup_id/'.$id.'.html">'.$groupname.'</option>';
       }
       
    }


    if (!empty($tempusergroup_id)){


      //已经拥有系统访问权限


          $showgrouplist=$config->where(' usergroup_id ="'.$tempusergroup_id.'" ')->select();
          $showallid='';
          for($j=0;$j<count($showgrouplist);$j++){
              $showhostgroup_id=$showgrouplist[$j]['squared_id'];
              $showallid=$showallid.','.$showhostgroup_id;
          }
          $showallid=substr($showallid,1,strlen($showallid));

          $myhostgroupoption="";

      if (!empty($showallid)){
        
          $myhostgrouplist=$squared->where(' id in ('.$showallid.') ')->select();
          for($i=0;$i<count($myhostgrouplist);$i++){
             $id=$myhostgrouplist[$i]['id'];
             $squared_title=$myhostgrouplist[$i]['title'];
             if ($tempsquared_id==$id){
                  $myhostgroupoption.='<option value="'.$id.'" selected="selected">'.$squared_title.'</option>';
             }else{
                  $myhostgroupoption.='<option value="'.$id.'">'.$squared_title.'</option>';
             }
          }
    
      //全部系统群组排除已经存在的

          $exitmyhostgroupoption="";
          $exitmyhostgrouplist=$squared->where(' id not in ('.$showallid.') ')->select();
          for($i=0;$i<count($exitmyhostgrouplist);$i++){
             $id=$exitmyhostgrouplist[$i]['id'];
             $squared_title=$exitmyhostgrouplist[$i]['title'];
             if ($tempsquared_id==$id){
                  $exitmyhostgroupoption.='<option value="'.$id.'" selected="selected">'.$squared_title.'</option>';
             }else{
                  $exitmyhostgroupoption.='<option value="'.$id.'">'.$squared_title.'</option>';
             }
          }
         
      }else{

          $exitmyhostgroupoption="";
          $exitmyhostgrouplist=$squared->select();
          for($i=0;$i<count($exitmyhostgrouplist);$i++){
             $id=$exitmyhostgrouplist[$i]['id'];
             $squared_title=$exitmyhostgrouplist[$i]['title'];
             if ($temphostgroup_id==$id){
                  $exitmyhostgroupoption.='<option value="'.$id.'" selected="selected">'.$squared_title.'</option>';
             }else{
                  $exitmyhostgroupoption.='<option value="'.$id.'">'.$squared_title.'</option>';
             }
          }


      }


    } 



    if(IS_POST){

        $usergroup_id=I('post.usergroup_id');
        $depart_id=I('post.depart_id');
        $selfhostgroup_id=$_REQUEST['selfhostgroup_id'];

        if(empty($usergroup_id)){$this->error('用户组不能为空!');}     
        if(empty($selfhostgroup_id)){$this->error('九宫格不能为空!');}
        
        for($i=0;$i<count($selfhostgroup_id);$i++){
            $dataList[]=array('squared_id'=>$selfhostgroup_id[$i],'usergroup_id'=>$usergroup_id);
        }

        $config->where('usergroup_id="'.$usergroup_id.'"')->delete();
        if($config->addAll($dataList)){
          $this->show('<script type="text/javascript" >alert("配置操作成功!");window.location.href="{:U(\"squared_manger?usergroup_id='.$usergroup_id.'\")}"; </script>');
          exit();
        }else{
          $this->error('配置操作有误');
        }

    }







    $usergroup_arr = $usergroup->field('id,groupname')->select();


    $this->assign('tempusergroup_id',$tempusergroup_id);
    $this->assign('showgroupname',$showgroupname);
    $this->assign('myhostgroupoption',$myhostgroupoption);
    $this->assign('exitmyhostgroupoption',$exitmyhostgroupoption);
    $this->assign('usergroupoption',$usergroupoption);
    $this->assign('checkusergroupoption',$checkusergroupoption);
    $this->assign('tempusergroup_id',$tempusergroup_id);

    $this->assign('usergroup_list',$usergroup_arr);
    $this->assign('url_flag','squared'); //left flag

    $this->display();
}








public function squared_truncate(){

    $User_squared_config = M('user_squared_config');
    $Resource_zabbix2 = M('resource_zabbix2');

    $usergroup_id = $_REQUEST['usergroup_id'];
    empty($usergroup_id) && $this->error('请输入ID');

    $where['usergroup_id'] = $usergroup_id;
    $del = $User_squared_config->where($where)->delete();
    if ($del) {
        $this->success("删除成功");
    }else{
        $this->error("删除失败");
    }
}


















}?>
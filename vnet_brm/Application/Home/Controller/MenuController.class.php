<?php
namespace  Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class MenuController extends HomeController{


/*
* by zwj 获取当前模块
*        
*/




//by zwj 时间戳转换天
function wordTime($time) {
    $time = (int) substr($time, 0, 10);
    $int = time() - $time;
    $str = '';
    if ($int <= 2){
        $str = sprintf('刚刚', $int);
    }elseif ($int < 60){
        $str = sprintf('%d秒前', $int);
    }elseif ($int < 3600){
        $str = sprintf('%d分钟前', floor($int / 60));
    }elseif ($int < 86400){
        $str = sprintf('%d小时前', floor($int / 3600));
    }elseif ($int < 2592000){
        $str = sprintf('%d天前', floor($int / 86400));
    }else{
        $str = date('Y-m-d H:i:s', $time);
    }
    return $str;
}

public function menulist(){
    
    $menu = M('ipdb_menu_zichan');

    $list=$menu->select();
    $deal_list = getTree($list);

    
    $this->assign('list',$deal_list);
    $this->assign('url_flag','menu'); //left flag
    $this->display();

}




public function addmenu(){

/*    header("Content-type: text/html; charset=utf-8");
    if(!is_login()){
        $this->redirect("User/login");
    }   
    $uid        =   is_login();
    $nickname = M('Member')->getFieldByUid($uid, 'nickname');*/

    $pidoption="";
    $Ipdb_menu = M('ipdb_menu_zichan');
    $list=$Ipdb_menu->where('pid=0')->order(' id asc')->select();

    for($i=0;$i<count($list);$i++){
        $id=$list[$i]['id'];
        $title=$list[$i]['title'];

        $pidoption.='<option value="'.$id.'"  >'.$title.'</option>';
        $list2=$Ipdb_menu->where('pid="'.$id.'" ')->order(' sort asc')->select();
        for($ii=0;$ii<count($list2);$ii++){
            $subid=$list2[$ii]['id'];
            $subtitle=$list2[$ii]['title'];
            $pidoption.='<option value="'.$subid.'"  >&nbsp;&nbsp;└'.$subtitle.'</option>';

            $list3=$Ipdb_menu->where('pid="'.$subid.'" ')->order(' sort asc')->select();
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
      $map['sort']=I('post.sort');
      $map['url']=I('post.url');
      $map['group']=I('post.group');
      $map['pid']=I('post.pid');
      $map['module']='home';
      $map['name']='Home/'.I('post.url');

      $list = $Ipdb_menu->where('title="'.$title.'"')->select();
      if(!empty($list)){
          $this->error('标题已经存在!');
      }
      if($Ipdb_menu->add($map)){
          $this->show('<script type="text/javascript" >alert("菜单添加成功");window.location.href="{:U("menulist")}"; </script>');
          exit();
      }else{
          $this->show('<script type="text/javascript" >alert("菜单添加失败");window.location.href="{:U("menulist")}"; </script>');
          exit();
      }

    }
    $this->assign('uid',$uid);
    $this->assign('nickname',$nickname);
    $this->assign('pidoption',$pidoption);
    $this->assign('url_flag','menu'); //left flag
    $this->display();
  }








public function usergroupaccesspost(){

  // if(!is_login()){
  //   $this->redirect("User/login");
  // }  
  // $uid        =   is_login();
  // $nickname = M('Member')->getFieldByUid($uid, 'nickname');


  $id=$_REQUEST['id'];
  empty($id) && $this->error('id不能为空');
  $rules=I('post.rules');
  $Ipdb_department = M('ipdb_department');
  $data['rules']=implode(",",$rules);

  $list=$Ipdb_department->where('id="'.$id.'"')->setField($data);
  if($list){
      $this->show('<script type="text/javascript" >alert("设置成功！");window.location.href="{:U(\"Ucenter/usergroupaccess?id='.$id.'\")}"; </script>');
      //$this->success('设置成功！',U("usergroupaccess?id=".$id));
      exit();
  }else{
      $this->show('<script type="text/javascript" >alert("设置失败！");window.location.href="{:U(\"Ucenter/usergroupaccess?id='.$id.'\")}"; </script>');
      //$this->success('设置失败！',U("usergroupaccess?id=".$id));
      exit();
  }


}

public function editmenu(){

    // if(!is_login()){
    //   $this->redirect("User/login");
    // }  
    // $uid        =   is_login();
    // $nickname = M('Member')->getFieldByUid($uid, 'nickname');


    $id   =  $_REQUEST['id']; 
    empty($id) && $this->error('id不能为空');
    $pid   =  $_REQUEST['pid']; 
    $Ipdb_menu = M('ipdb_menu_zichan');
    $list=$Ipdb_menu->where('id="'.$id.'"')->select();
    $title=$list[0]['title']; 
    $sort=$list[0]['sort']; 
    $pid=$list[0]['pid']; 
    if (empty($pid)){
        $pidstr='无';
    }else{
        $list2=$Ipdb_menu->where('id="'.$pid.'"')->select();
        $pidstr=$list2[0]['title'];
    }
    $url=$list[0]['url']; 
    $group=$list[0]['group']; 

//-----------------------------------------------------菜单下拉----------------------------------------------------------
    $pidoption="";
    $list=$Ipdb_menu->where('pid=0')->order(' id asc')->select();
    for($i=0;$i<count($list);$i++){
        $tempid=$list[$i]['id'];
        $temptitle=$list[$i]['title'];
        
        if ($tempid==$pid){
            $pidoption.='<option value="'.$tempid.'" selected="selected" >'.$temptitle.'</option>';
        }else{
            $pidoption.='<option value="'.$tempid.'"  >'.$temptitle.'</option>';
        }

        $list2=$Ipdb_menu->where('pid="'.$tempid.'" ')->order(' sort asc')->select();
        for($ii=0;$ii<count($list2);$ii++){
            $subid=$list2[$ii]['id'];
            $subpid=$list2[$ii]['pid'];
            $subtitle=$list2[$ii]['title'];

            if ($subid==$pid){
                $pidoption.='<option value="'.$subid.'" selected="selected" >&nbsp;&nbsp;└'.$subtitle.'</option>';
            }else{
                $pidoption.='<option value="'.$subid.'"  >&nbsp;&nbsp;└'.$subtitle.'</option>';
            }
            
            $list3=$Ipdb_menu->where('pid="'.$subid.'" ')->order(' sort asc')->select();
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
        $data['sort']=I('post.sort');
        $data['url']=I('post.url');
        $data['group']=I('post.group');
        //$data['module']='home';
        $data['name']='Home/'.I('post.url');

        $list3=$Ipdb_menu->where('id="'.$id.'"')->setField($data);
        if($list3){
          if (!empty($id) && !empty($pid)){
              $this->show('<script type="text/javascript" >alert("修改成功！");window.location.href="{:U(\"menulist?id='.$id.'&pid='.$pid.'\")}"; </script>');
          }else{
              $this->show('<script type="text/javascript" >alert("修改成功！");window.location.href="{:U("menulist")}"; </script>');
          }
          exit();
        }else{
          if (!empty($id) && !empty($pid)){
              $this->show('<script type="text/javascript" >alert("修改失败！");window.location.href="{:U(\"menulist?id='.$id.'&pid='.$pid.'\")}"; </script>');
          }else{
              $this->show('<script type="text/javascript" >alert("修改失败！");window.location.href="{:U("menulist")}"; </script>');
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


public function menudelete(){
 
/*    if(!is_login()){
      $this->redirect("User/login");
    }    
    $uid        =   is_login();
    $nickname = M('Member')->getFieldByUid($uid, 'nickname');*/


    $id=I('get.id');
    empty($id) && $this->error('id不能为空');

    $Ipdb_menu=M('ipdb_menu_zichan');
    $list=$Ipdb_menu->where('id="'.$id.'"')->delete();
    $this->show('<script type="text/javascript" >alert("菜单删除成功");window.location.href="{:U("menulist")}"; </script>');  

    
}











}?>

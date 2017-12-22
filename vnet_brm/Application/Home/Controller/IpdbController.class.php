<?php
namespace  Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class IpdbController extends HomeController{



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



public function itemcontracttypes(){
    if(!is_login()){
      $this->redirect("User/login");
    }    

    $uid        =   is_login();
    

    $name=$_REQUEST['name'];
    $contypeid=$_REQUEST['contypeid'];
    $Ipdb_contracttypes = M('ipdb_contracttypes'); 

$order=" sort asc";
    if (!empty($name)){

        if (!empty($contypeid)){
            $count= $Ipdb_contracttypes->where(' contypeid="'.$contypeid.'" and name like "%'.$name.'%" ')->count();
            $page = new \Think\Page($count,50);
            $list=$Ipdb_contracttypes->where(' contypeid="'.$contypeid.'" and name like "%'.$name.'%" ')->order($order)->limit($page->firstRow.','.$page->listRows)->order($order)->select(); 
        }else{
            $count= $Ipdb_contracttypes->where(' contypeid=0  and name like "%'.$name.'%"')->count();
            $page = new \Think\Page($count,50);
            $list=$Ipdb_contracttypes->where(' contypeid=0  and name like "%'.$name.'%"')->order($order)->limit($page->firstRow.','.$page->listRows)->order($order)->select();      
        }    

    }else{

        if (!empty($contypeid)){
            $count= $Ipdb_contracttypes->where(' contypeid="'.$contypeid.'" ')->count();
            $page = new \Think\Page($count,50);
            $list=$Ipdb_contracttypes->where(' contypeid="'.$contypeid.'" ')->order($order)->limit($page->firstRow.','.$page->listRows)->order($order)->select(); 
        }else{
            $count= $Ipdb_contracttypes->where(' contypeid=0 ')->count();
            $page = new \Think\Page($count,50);
            $list=$Ipdb_contracttypes->where(' contypeid=0 ')->order($order)->limit($page->firstRow.','.$page->listRows)->order($order)->select();      
        }

    }

    for($i=0;$i<count($list);$i++){
        $temppid=$list[$i]['contypeid'];
        $checklist= $Ipdb_contracttypes->where(' id="'.$temppid.'" ')->select();
        $list[$i]['contypestr']=$checklist[0]['name'];
        if (empty($list[$i]['contypestr'])){$list[$i]['contypestr']='无';}

    }


    $this->assign('url_flag','lei_index');
    $this->assign('uid',$uid);
    $this->assign('nickname',$nickname);
    $this->assign('name',$name);

    $this->assign('sort',$sort); 
    $page->setConfig('header','共');
    $page->setConfig('first','«');
    $page->setConfig('prev','‹');
    $page->setConfig('next','›');
    $page->setConfig('last','»');
    $page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
    $show = $page->show();

    $this->assign('list',$list);
    $this->assign('page',$show);
    $this->display();

  }



public function  delitemcontracttypes(){

  if(!is_login()){
    $this->redirect("User/login");
  }    

  $uid        =   is_login();
  // 

  $Ipdb_contracttypes=M('ipdb_contracttypes');

  $id=I('get.id');
  empty($id) && $this->error('id不能为空'); 
  $list = $Ipdb_contracttypes->where('contypeid ="'.$id.'" ')->select();
  if(!empty($list)){
      $this->error('该类别下还有数据请先清空数据再删除!');
  } 
   
  $Ipdb_contracttypes->where('id="'.$id.'"')->delete();
  $this->show('<script type="text/javascript" >alert("合同类型删除成功");window.location.href="index.php?s=/Home/Ipdb/itemcontracttypes.html"; </script>'); 
  exit();

}



public function itemcontracttypesadd(){

    // header("Content-type: text/html; charset=utf-8");
    if(!is_login()){
        $this->redirect("User/login");
    }   
    $uid        =   is_login();
    // 

    $contypeoption="";
    $Ipdb_contracttypes = M('ipdb_contracttypes');
    $list=$Ipdb_contracttypes->where('contypeid=0')->order(' id asc')->select();
//---------------------------------------合同类型树状结构图

    for($i=0;$i<count($list);$i++){
        $id=$list[$i]['id'];
        $name=$list[$i]['name'];

        $contypeoption.='<option value="'.$id.'"  >'.$name.'</option>';
        $list2=$Ipdb_contracttypes->where('contypeid="'.$id.'" ')->order(' sort asc')->select();
        for($ii=0;$ii<count($list2);$ii++){
            $subid=$list2[$ii]['id'];
            $subtitle=$list2[$ii]['name'];
            $contypeoption.='<option value="'.$subid.'"  >&nbsp;&nbsp;└'.$subtitle.'</option>';

            $list3=$Ipdb_contracttypes->where('contypeid="'.$subid.'" ')->order(' sort asc')->select();
            for($iii=0;$iii<count($list3);$iii++){
                $threeid=$list3[$iii]['id'];
                $threetitle=$list3[$iii]['name'];
                $contypeoption.='<option value="'.$threeid.'"  >&nbsp;&nbsp;&nbsp;&nbsp;└'.$threetitle.'</option>';
            }

            $list4=$Ipdb_contracttypes->where('contypeid="'.$threeid.'" ')->order(' sort asc')->select();
            for($iiii=0;$iiii<count($list4);$iiii++){
                $fourid=$list4[$iiii]['id'];
                $fouridtitle=$list4[$iiii]['name'];
                $contypeoption.='<option value="'.$fourid.'"  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└'.$fouridtitle.'</option>';
            }



        }

    }


    if(IS_POST){
      $name=I('post.name');

      $map['name']=I('post.name');
      $map['sort']=I('post.sort');
      $map['contypeid']=I('post.contypeid');

      $list = $Ipdb_contracttypes->where('name="'.$name.'"')->select();
      if(!empty($list)){
          $this->error('合同类型标题已经存在!');
      }
      if($Ipdb_contracttypes->add($map)){
          $this->show('<script type="text/javascript" >alert("合同类型添加成功");window.location.href="{:U("itemcontracttypes")}"; </script>');
      }else{
          $this->show('<script type="text/javascript" >alert("合同类型添加失败");window.location.href="{:U("itemcontracttypes")}"; </script>');
      }
      exit();
    }

    $this->assign('url_flag','lei_index');
    $this->assign('uid',$uid);
    $this->assign('nickname',$nickname);
    $this->assign('contypeoption',$contypeoption);
    $this->display();
  }




public function edititemcontracttypes(){

 
    if(!is_login()){
      $this->redirect("User/login");
     }    

     $uid        =   is_login();
     

    $id=$_REQUEST['id'];
    empty($id) && $this->error('参数不能为空');
    $Ipdb_contracttypes=M('ipdb_contracttypes');

    $contracttypeslist=$Ipdb_contracttypes->where('id = "'.$id.'" ')->select();
    $id=$contracttypeslist[0]['id']; 
    $contypeid=$contracttypeslist[0]['contypeid']; 
    $name=$contracttypeslist[0]['name']; 
    $sort=$contracttypeslist[0]['sort']; 


//-----------------------------------------------------菜单下拉----------------------------------------------------------
    $contypeoption="";
    $list=$Ipdb_contracttypes->where('contypeid=0')->order(' sort asc')->select();
    for($i=0;$i<count($list);$i++){
        $tempid=$list[$i]['id'];
        $temptitle=$list[$i]['name'];
        
        if ($tempid==$contypeid){
            $contypeoption.='<option value="'.$tempid.'" selected="selected" >'.$temptitle.'</option>';
        }else{
            $contypeoption.='<option value="'.$tempid.'"  >'.$temptitle.'</option>';
        }

        $list2=$Ipdb_contracttypes->where('contypeid="'.$tempid.'" ')->order(' sort asc')->select();
        for($ii=0;$ii<count($list2);$ii++){
            $subid=$list2[$ii]['id'];
            $subpid=$list2[$ii]['contypeid'];
            $subtitle=$list2[$ii]['name'];

            if ($subid==$contypeid){
                $contypeoption.='<option value="'.$subid.'" selected="selected" >&nbsp;&nbsp;└'.$subtitle.'</option>';
            }else{
                $contypeoption.='<option value="'.$subid.'"  >&nbsp;&nbsp;└'.$subtitle.'</option>';
            }
            
            $list3=$Ipdb_contracttypes->where('contypeid="'.$subid.'" ')->order(' sort asc')->select();
            for($iii=0;$iii<count($list3);$iii++){
                $threeid=$list3[$iii]['id'];
                $threetitle=$list3[$iii]['name'];
                if ($threeid==$contypeid){
                    $contypeoption.='<option value="'.$threeid.'" selected="selected" >&nbsp;&nbsp;&nbsp;&nbsp;└'.$threetitle.'</option>';
                }else{
                    $contypeoption.='<option value="'.$threeid.'"  >&nbsp;&nbsp;&nbsp;&nbsp;└'.$threetitle.'</option>';
                }
            }            
        }



    }

//---------------------------------------------------------------------------------------------------------------





    if ( IS_POST ) {

      $id=$_REQUEST['id'];
      empty($id) && $this->error('参数不能为空'); 
      $editdata['contypeid']= I('post.contypeid');
      $editdata['name']= I('post.name');
      $editdata['sort']= I('post.sort');

      $Ipdb_contracttypes->where('id="'.$id.'"')->setField($editdata);
      $this->show('<script type="text/javascript" >alert("合同类型编辑成功");window.location.href="{:U("Ipdb/itemcontracttypes")}"; </script>');
      exit();

    }


    $this->assign('url_flag','lei_index');
    $this->assign('id',$id);
    $this->assign('contypeid',$contypeid);
    $this->assign('name',$name);
    $this->assign('sort',$sort);
    $this->assign('contypeoption',$contypeoption);
    $this->assign('list',$list); 
    $this->display();
}




















  public function  itemtypes(){
 
    if(!is_login()){
      $this->redirect("User/login");
     }    

     $uid        =   is_login();
     

    $Member =M("member");
    $Ipdb_itemtypes =M("ipdb_itemtypes");

    $ipdblist=$Ipdb_itemtypes->order('typedesc asc')->select(); 

    for($i=0;$i<count($ipdblist);$i++){
        $hassoftware=$ipdblist[$i]['hassoftware'];    
        if ($hassoftware==0){$hassoftware='不支持';}else{$hassoftware='支持';}
        $ipdblist[$i]['hassoftware']=$hassoftware;
    }


    if ( IS_POST ) {

      $typedesc=$_REQUEST['typedesc'];
      $hassoftware=$_REQUEST['hassoftware'];

      $clist=$Ipdb_itemtypes->where('typedesc="'.$typedesc.'" ')->order('id desc')->select();
      $lastid=$clist[0]['id'];
      if (empty($lastid)){
          $adddata['typedesc']= $typedesc;
          $adddata['hassoftware']= $hassoftware;
          $list=$Ipdb_itemtypes->data($adddata)->add();
          if (!empty($list)){
              $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="index.php?s=/Home/Ipdb/itemtypes.html"; </script>');
          }else{
              $this->show('<script type="text/javascript" >alert("重复名称，请重新添加");window.location.href="index.php?s=/Home/Ipdb/itemtypes.html"; </script>');
          }
          exit();
      }

        // if ($flag=='sort'){
        //     empty($id) && $this->error('参数缺失，ID');
        //     $data['nposition']= I('post.nposition_id');
        //     $Flow_slottouser->where('id="'.$id.'"')->setField($data);
        //     $this->show('<script type="text/javascript" >alert("排序成功");window.location.href="index.php?s=/Home/Flow/showmail/strname/'.$strname.'/tid/'.$templateid.'.html"; </script>');

        // }

    }

    $this->assign('url_flag','listlocations');

    $this->assign('list',$ipdblist);
    $this->display();   
  } 






public function edititemtypes(){

 
    if(!is_login()){
      $this->redirect("User/login");
     }    

     $uid        =   is_login();
     

    $id=$_REQUEST['id'];
    $hassoftware=$_REQUEST['hassoftware'];
    $typedesc=$_REQUEST['typedesc'];
    empty($id) && $this->error('参数不能为空');
    $Ipdb_itemtypes=M('ipdb_itemtypes');

    $itemtypeslist=$Ipdb_itemtypes->where('id = "'.$id.'" ')->select();
    $id=$itemtypeslist[0]['id']; 
    $hassoftware=$itemtypeslist[0]['hassoftware']; 
    $typedesc=$itemtypeslist[0]['typedesc']; 

    if ( IS_POST ) {

      $id=$_REQUEST['id'];
      empty($id) && $this->error('参数不能为空'); 
      $data['typedesc']= I('post.typedesc');
      $data['hassoftware']= I('post.hassoftware');

      $Ipdb_itemtypes->where('id="'.$id.'"')->setField($data);
      $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Ipdb/itemtypes")}"; </script>');
      exit();

    }


    $this->assign('url_flag','listlocations');
    $this->assign('id',$id);
    $this->assign('hassoftware',$hassoftware);
    $this->assign('typedesc',$typedesc);
    $this->assign('list',$list); 
    $this->display();
}



public function  delitemtypes(){

  if(!is_login()){
    $this->redirect("User/login");
  }    

  $uid        =   is_login();
  // 
  $Ipdb_itemtypes=M('ipdb_itemtypes');

  $id=I('get.id');
  empty($id) && $this->error('id不能为空'); 

  $list=$Ipdb_itemtypes->where('id="'.$id.'"')->delete();
  $this->show('<script type="text/javascript" >alert("硬件类型删除成功");window.location.href="index.php?s=/Home/Ipdb/itemtypes.html"; </script>'); 
  exit();
}





  public function  itemstatustypes(){
 
    if(!is_login()){
      $this->redirect("User/login");
     }    

     $uid        =   is_login();
     
    $Ipdb_statustypes =M("ipdb_statustypes");
    $Ipdb_colortypes =M("ipdb_colortypes");

    $ipdbcolorlist=$Ipdb_colortypes->order('id desc')->select(); 
    $coloroption="";
    for($i=0;$i<count($ipdbcolorlist);$i++){
       $color=$ipdbcolorlist[$i]['color'];
       $colorname=$ipdbcolorlist[$i]['colorname'];
       $coloroption.='<option value="'.$color.'" style="background-color:#'.$color.'">'.$colorname.'</option>';

    }

    $ipdbstatuslist=$Ipdb_statustypes->order('id desc')->select();    
    for($i=0;$i<count($ipdbstatuslist);$i++){
        $color=$ipdbstatuslist[$i]['color']; 
        $tempipdbcolorlist=$Ipdb_colortypes->where('color="'.$color.'"')->select();
        $colorname=$tempipdbcolorlist[0]['colorname'];
        $ipdbstatuslist[$i]['colorstr']=$colorname;
    }


    if ( IS_POST ) {

      $adddata['statusdesc']= $_REQUEST['statusdesc'];
      $adddata['color']= $_REQUEST['color'];
      $Ipdb_statustypes->data($adddata)->add();
      $this->show('<script type="text/javascript" >alert("状态类型添加成功");window.location.href="{:U("Ipdb/itemstatustypes")}"; </script>');
      exit();


    }

    $this->assign('url_flag','listlocations');
    $this->assign('list',$ipdbstatuslist);
    $this->assign('coloroption',$coloroption);
    $this->display();   
  } 


  public function  addstatustypes(){
 
    if(!is_login()){
      $this->redirect("User/login");
     }    

     $uid        =   is_login();
     
    $Ipdb_statustypes =M("ipdb_statustypes");
    $Ipdb_colortypes =M("ipdb_colortypes");

    $ipdbcolorlist=$Ipdb_colortypes->order('id desc')->select(); 
    $coloroption="";
    for($i=0;$i<count($ipdbcolorlist);$i++){
       $color=$ipdbcolorlist[$i]['color'];
       $colorname=$ipdbcolorlist[$i]['colorname'];
       $coloroption.='<option value="'.$color.'" style="background-color:#'.$color.'">'.$colorname.'</option>';

    }


    if ( IS_POST ) {

      $adddata['statusdesc']= $_REQUEST['statusdesc'];
      $adddata['color']= $_REQUEST['color'];
      $Ipdb_statustypes->data($adddata)->add();
      $this->show('<script type="text/javascript" >alert("状态类型添加成功");window.location.href="{:U("Ipdb/itemstatustypes")}"; </script>');
      exit();

    }

    $this->assign('url_flag','listlocations');

    $this->assign('coloroption',$coloroption);
    $this->display();   
  } 











public function edititemstatustypes(){

    if(!is_login()){
      $this->redirect("User/login");
     }    

     $uid        =   is_login();
     


    $id=$_REQUEST['id'];
    $hassoftware=$_REQUEST['hassoftware'];
    $typedesc=$_REQUEST['typedesc'];
    empty($id) && $this->error('参数不能为空');
    $Ipdb_statustypes =M("ipdb_statustypes");

    $itemstatuslist=$Ipdb_statustypes->where('id = "'.$id.'" ')->select();
    $id=$itemstatuslist[0]['id']; 
    $statusdesc=$itemstatuslist[0]['statusdesc']; 
    $color=$itemstatuslist[0]['color']; 


    $Ipdb_colortypes =M("ipdb_colortypes");

    $ipdbcolorlist=$Ipdb_colortypes->order('id desc')->select(); 
    $coloroption="";
    for($i=0;$i<count($ipdbcolorlist);$i++){
       $tempcolor=$ipdbcolorlist[$i]['color'];
       $colorname=$ipdbcolorlist[$i]['colorname'];
       if ($color==$tempcolor){
           $coloroption.='<option value="'.$tempcolor.'" selected="selected" style="background-color:#'.$tempcolor.'">'.$colorname.'</option>';
       }else{
           $coloroption.='<option value="'.$tempcolor.'" style="background-color:#'.$tempcolor.'">'.$colorname.'</option>';
       }

    }


    if ( IS_POST ) {

      $id=$_REQUEST['id'];
      empty($id) && $this->error('参数不能为空'); 
      $data['statusdesc']= I('post.statusdesc');
      $data['color']= I('post.color');

      $Ipdb_statustypes->where('id="'.$id.'"')->setField($data);
      $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Ipdb/itemstatustypes")}"; </script>');
      exit();

    }


    $this->assign('url_flag','listlocations');

    $this->assign('id',$id);
    $this->assign('color',$color);
    $this->assign('statusdesc',$statusdesc);
    $this->assign('coloroption',$coloroption);
    $this->assign('list',$list); 
    $this->display();
}



public function  delitemstatustypes(){

  if(!is_login()){
    $this->redirect("User/login");
  }    

  $uid        =   is_login();

  $Ipdb_statustypes =M("ipdb_statustypes");

  $id=I('get.id');
  empty($id) && $this->error('id不能为空'); 

  $Ipdb_statustypes->where('id="'.$id.'"')->delete();
  $this->show('<script type="text/javascript" >alert("状态类型删除成功");window.location.href="{:U("Ipdb/itemstatustypes")}"; </script>'); 
  exit();
}




public function  itemfiletypes(){

  if(!is_login()){
    $this->redirect("User/login");
   }    

   $uid        =   is_login();
   
  $Ipdb_filetypes =M("ipdb_filetypes");
  $typedesc=$_REQUEST['typedesc'];
  if (!empty($typedesc)){
      $filetypelist=$Ipdb_filetypes->where('typedesc like "%'.$typedesc.'%" ')->order('id desc')->select(); 
  }else{
      $filetypelist=$Ipdb_filetypes->order('id desc')->select(); 
  }

  if ( IS_POST ) {

    $adddata['typedesc']= $_REQUEST['typedesc'];
    $Ipdb_filetypes->data($adddata)->add();
    $this->show('<script type="text/javascript" >alert("文本类型添加成功");window.location.href="{:U("Ipdb/itemfiletypes")}"; </script>');
    exit();

  }

  $this->assign('url_flag','listlocations');

  $this->assign('list',$filetypelist);
  $this->display();   
} 



  public function  addfiletypes(){
 
    if(!is_login()){
      $this->redirect("User/login");
     }    

     $uid        =   is_login();
     

    $Ipdb_filetypes =M("ipdb_filetypes");


    if ( IS_POST ) {
      $adddata['typedesc']= $_REQUEST['typedesc'];
      $Ipdb_filetypes->data($adddata)->add();
      $this->show('<script type="text/javascript" >alert("文本类型添加成功");window.location.href="{:U("Ipdb/itemfiletypes")}"; </script>');
      exit();
    }

    $this->assign('url_flag','listlocations');

    $this->display();   
  } 

public function editfiletypes(){

 
    if(!is_login()){
      $this->redirect("User/login");
     }    

     $uid        =   is_login();
     

    $id=$_REQUEST['id'];
    empty($id) && $this->error('参数不能为空');
    $Ipdb_filetypes =M("ipdb_filetypes");

    $itemfiletypeslist=$Ipdb_filetypes->where('id = "'.$id.'" ')->select();
    $id=$itemfiletypeslist[0]['id']; 
    $typedesc=$itemfiletypeslist[0]['typedesc']; 

    if ( IS_POST ) {

      $id=$_REQUEST['id'];
      empty($id) && $this->error('参数不能为空'); 
      $data['typedesc']= I('post.typedesc');

      $Ipdb_filetypes->where('id="'.$id.'"')->setField($data);
      $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Ipdb/itemfiletypes")}"; </script>');
      exit();

    }


    $this->assign('url_flag','listlocations');

    $this->assign('id',$id);
    $this->assign('typedesc',$typedesc);
    $this->assign('list',$list); 
    $this->display();
}



public function  delfiletypes(){

  if(!is_login()){
    $this->redirect("User/login");
  }    

  $uid        =   is_login();
  // 
  $Ipdb_filetypes =M("ipdb_filetypes");

  $id=I('get.id');
  empty($id) && $this->error('id不能为空'); 

  $Ipdb_filetypes->where('id="'.$id.'"')->delete();
  $this->show('<script type="text/javascript" >alert("文件类型删除成功");window.location.href="{:U("Ipdb/itemfiletypes")}"; </script>'); 
  exit();
}



  public function  itemcolorlist(){
 
    if(!is_login()){
      $this->redirect("User/login");
     }    

     $uid        =   is_login();

    $Ipdb_colortypes =M("ipdb_colortypes");


    $ipdbcolorlist=$Ipdb_colortypes->order('id desc')->select();    

    if ( IS_POST ) {

      $adddata['colorname']= $_REQUEST['colorname'];
      $adddata['color']= $_REQUEST['color'];
      $Ipdb_colortypes->data($adddata)->add();
      $this->show('<script type="text/javascript" >alert("颜色类型添加成功");window.location.href="{:U("Ipdb/itemcolorlist")}"; </script>');
      exit();

    }

    $this->assign('url_flag','listlocations');

    $this->assign('list',$ipdbcolorlist);
    $this->display();   
  } 






public function  delcolortypes(){

  if(!is_login()){
    $this->redirect("User/login");
  }    

  $uid        =   is_login();
  // 
  $Ipdb_colortypes =M("ipdb_colortypes");

  $id=I('get.id');
  empty($id) && $this->error('id不能为空'); 

  $Ipdb_colortypes->where('id="'.$id.'"')->delete();
  $this->show('<script type="text/javascript" >alert("颜色类型删除成功");window.location.href="{:U("Ipdb/itemcolorlist")}"; </script>'); 
  exit();
}



public function  addtagtypes(){

  if(!is_login()){
    $this->redirect("User/login");
   }    

   $uid        =   is_login();
   

  $Ipdb_tags =M("ipdb_tags");
  $Ipdb_tag2item =M("ipdb_tag2item");
  $Ipdb_tag2software =M("ipdb_tag2software");

  if ( IS_POST ) {

    $adddata['name']= $_REQUEST['name'];
    $Ipdb_tags->data($adddata)->add();
    $this->show('<script type="text/javascript" >alert("标记添加成功");window.location.href="{:U("Ipdb/itemtagtypes")}"; </script>');
    exit();

  }

  $this->assign('url_flag','listlocations');

  $this->assign('list',$taglist);
  $this->display();   
} 




  public function  itemtagtypes(){
 
    if(!is_login()){
      $this->redirect("User/login");
     }    

     $uid        =   is_login();
     

    $Ipdb_tags =M("ipdb_tags");
    $Ipdb_tag2item =M("ipdb_tag2item");
    $Ipdb_tag2software =M("ipdb_tag2software");

    $tag_name=$_REQUEST['tag_name'];
    if (!empty($tag_name)){
        $taglist=$Ipdb_tags->where('name like "%'.$tag_name.'%"')->order('id desc')->select();
    }else{
        $taglist=$Ipdb_tags->order('id desc')->select();
    }

        
    for($i=0;$i<count($taglist);$i++){
        $tempid=$taglist[$i]['id']; 
        $tag2count=$Ipdb_tag2item->where('tagid="'.$tempid.'"')->count();
        $tag2softwarecount=$Ipdb_tag2software->where('tagid="'.$tempid.'"')->count();
        if ($tag2count==''){ $tag2count=0;}
        if ($tag2softwarecount==''){ $tag2softwarecount=0;}
        $taglist[$i]['tag2count']=$tag2count;
        $taglist[$i]['tag2softwarecount']=$tag2softwarecount;
    }
/*    if ( IS_POST ) {

      $adddata['name']= $_REQUEST['name'];
      $Ipdb_tags->data($adddata)->add();
      $this->show('<script type="text/javascript" >alert("标记添加成功");window.location.href="{:U("Ipdb/itemtagtypes")}"; </script>');
      exit();

    }*/

    $this->assign('url_flag','listlocations');
    $this->assign('list',$taglist);
    $this->display();   
  } 





public function edittagtypes(){

 
    if(!is_login()){
      $this->redirect("User/login");
     }    

     $uid        =   is_login();
     
    $id=$_REQUEST['id'];
    empty($id) && $this->error('参数不能为空');
    $Ipdb_tags =M("ipdb_tags");

    $itemtaglist=$Ipdb_tags->where('id = "'.$id.'" ')->select();
    $id=$itemtaglist[0]['id']; 
    $name=$itemtaglist[0]['name']; 

    if ( IS_POST ) {

      $id=$_REQUEST['id'];
      empty($id) && $this->error('参数不能为空'); 
      $data['name']= I('post.name');

      $Ipdb_tags->where('id="'.$id.'"')->setField($data);
      $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Ipdb/itemtagtypes")}"; </script>');
      exit();

    }
    $this->assign('url_flag','listlocations');
    $this->assign('id',$id);
    $this->assign('name',$name);
    $this->display();
}



public function  deltagtypes(){

  if(!is_login()){
    $this->redirect("User/login");
  }    

  $uid        =   is_login();
  // 
  $Ipdb_tags =M("ipdb_tags");

  $id=I('get.id');
  empty($id) && $this->error('id不能为空'); 

  $Ipdb_tags->where('id="'.$id.'"')->delete();
  $this->show('<script type="text/javascript" >alert("标记删除成功");window.location.href="{:U("Ipdb/itemtagtypes")}"; </script>'); 
  exit();
}




  public function itemtag2list(){

    if(!is_login()){
      $this->redirect("User/login");
    } 

    $id=$_REQUEST['id'];
    empty($id) && $this->error('请输入id');
    $Ipdb_tags =M("ipdb_tags");
    $Ipdb_tag2item =M("ipdb_tag2item");
    //$Ipdb_tag2software =M("ipdb_tag2software");
    $list=$Ipdb_tag2item->where('tagid="'.$id.'"')->select();
    
    for($i=0;$i<count($list);$i++){

      $tagid=$list[$i]['tagid'];
      $tagslist=$Ipdb_tags->where(' id ="'.$tagid.'"')->field('name')->select();
      $tagname=$tagslist[0]['name'];
      $list[$i]['tagname']=$tagname;

    }
    $this->assign('url_flag','listlocations');
    $this->assign('list',$list);
    $this->display();
  }




  public function itemtag2softlist(){

    if(!is_login()){
      $this->redirect("User/login");
    } 

    $id=$_REQUEST['id'];
    empty($id) && $this->error('请输入id');
    $Ipdb_tags =M("ipdb_tags");
    $Ipdb_tag2software =M("ipdb_tag2software");
    $list=$Ipdb_tag2software->where('tagid="'.$id.'"')->select();
    
    for($i=0;$i<count($list);$i++){

      $tagid=$list[$i]['tagid'];
      $tagslist=$Ipdb_tags->where(' id ="'.$tagid.'"')->field('name')->select();
      $tagname=$tagslist[0]['name'];
      $list[$i]['tagname']=$tagname;

    }
    $this->assign('url_flag','listlocations');
    $this->assign('list',$list);
    $this->display();
  }





  public function  listlocations(){
 
    if(!is_login()){
       $this->redirect("User/login");
    }    

    $uid        =   is_login();
    

    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    $Ipdb_items =M("ipdb_items");

    $name= $_REQUEST['name'];
    if (empty($name)){
        $locationlist=$Ipdb_locations->order('id desc')->select();  
    }else{
        $locationlist=$Ipdb_locations->where('name like "%'.$name.'%"')->order('id desc')->select();  

    }
    for($i=0;$i<count($locationlist);$i++){
        $tempid=$locationlist[$i]['id']; 
        $areacount=$Ipdb_locareas->where('locationid="'.$tempid.'"')->count();
        $locationlist[$i]['areacount']=$areacount;
        $itemcount=$Ipdb_items->where('locationid="'.$tempid.'"')->count();
        $locationlist[$i]['itemcount']=$itemcount;

    }

    $this->assign('url_flag','listlocations');
    $this->assign('list',$locationlist);
    $this->assign('name',$name);
    $this->display();   
  } 




  public function  addlocation(){
 
    if(!is_login()){
        $this->redirect("User/login");
    }    

    $uid        =   is_login();
    

    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    $Ipdb_node =M("ipdb_node");

    $aDataList=$Ipdb_node->order(' id asc')->select();//where('contypeid=0')->
    $aDataList = $this->array_column($aDataList, null, 'id');
    $aTreeList = $this->getTreeList(0, $aDataList);//获取 下面 层次 用 getTreeList
    $contypeoption = $this->createOptions($aTreeList);





    if ( IS_POST ) {

        $name= $_REQUEST['name'];
        $area= $_REQUEST['area'];
        $contypeid= $_REQUEST['contypeid'];
        //$floor= $_REQUEST['floor'];
        $floorplanfn= $_REQUEST['floorplanfn'];

        $loclist=$Ipdb_locations->where('name="'.$name.'"')->select();
        $checkid=$loclist['id'];
        !empty($checkid) && $this->error('重复的数据中心名称!');

        if (!empty($_FILES['floorplanfn'])){
          //--------------------上传平面图插入------------------by zwj
         
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     9145728 ;// 设置附件上传大小
            $upload->exts      =     array('bmp','jpg','jpeg','png','gif');// 设置附件上传类型
            $upload->rootPath  =      './upload/'; // 设置附件上传根目录
            $upload->saveName = 'time';
            // 上传单个文件
            //var_dump($_FILES);
            $info   =   $upload->uploadOne($_FILES['floorplanfn']);
            if(!$info) {// 上传错误提示错误信息
                //$this->error($upload->getError());
            }else{// 上传成功 获取上传文件信息
                //__ROOT__.'/attachments/vnet_'.$lasthistoryid.'/'.$now.'/111';
                //echo $info['savepath'].'===='.$info['name'].'----'.$info['ext'];//文件扩展名
                $date=date("Y-m-d");
                $savename=time();
                $floorplanfn='/upload/'.$date.'/'.$info['name'];//$info['name']
                $savename='/upload/'.$date.'/'.$savename.'.'.$info['ext'];

            }

          //-------------------------------------------------------------
        }

        $adddata['area']= $area;
        $adddata['name']= $name;
        $adddata['nodeid']= $contypeid;
        //$adddata['floor']= $floor;
        $adddata['floorplanfn']= $floorplanfn;
        $adddata['savename']= $savename;
        //insert by zwj
        $Ipdb_locations->data($adddata)->add();

        $this->show('<script type="text/javascript" >alert("数据中心添加成功");window.location.href="{:U("Ipdb/listlocations")}"; </script>');
        exit();         

    }



    $this->assign('url_flag','listlocations');
    $this->assign('list',$locationlist);
    $this->assign('name',$name); 
    //$this->assign('rackcount',$rackcount); //关联机架数量=获取 by zwj
    $this->assign('area',$area); 
    $this->assign('savename',$savename); 
    $this->assign('floor',$floor); 
    $this->assign('floorplanfn',$floorplanfn); 
    $this->assign('contypeoption',$contypeoption); 
    $this->display();   
  } 




  public function  editlocation(){
 
    if(!is_login()){
       $this->redirect("User/login");
    }    

    $uid        =   is_login();
    

    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    $Ipdb_node =M("ipdb_node");

    $id=$_REQUEST['id'];
    empty($id) && $this->error('参数不能为空');

    $locationslist=$Ipdb_locations->where('id = "'.$id.'" ')->select();
    $locationid=$locationslist[0]['id']; 
    $area=$locationslist[0]['area']; 
    $name=$locationslist[0]['name']; 
    $nodeid=$locationslist[0]['nodeid']; 
    //$floor=$locationslist[0]['floor']; 
    $floorplanfn=$locationslist[0]['floorplanfn']; 
    $savename=$locationslist[0]['savename']; 

    $locareaslist=$Ipdb_locareas->where('locationid = "'.$locationid.'" ')->select();
    $areaname=$locareaslist[0]['areaname'];


    $aDataList=$Ipdb_node->order(' id asc')->select();//where('contypeid=0')->
    $aDataList = $this->array_column($aDataList, null, 'id');
    $aTreeList = $this->getTreeList(0, $aDataList);//获取 下面 层次 用 getTreeList
    $contypeoption = $this->createOptions($aTreeList,$nodeid);


    if ( IS_POST ) {

          $id= $_REQUEST['id'];
          $name= $_REQUEST['name'];
          $area= $_REQUEST['area'];
          //$floor= $_REQUEST['floor'];
          $contypeid= $_REQUEST['contypeid'];

          if (!empty($_FILES['floorplanfn']['name'])){
            //--------------------上传平面图插入------------------by zwj
           
              $upload = new \Think\Upload();// 实例化上传类
              $upload->maxSize   =     9145728 ;// 设置附件上传大小
              $upload->exts      =     array('bmp','jpg','jpeg','png','gif');// 设置附件上传类型
              $upload->rootPath  =      './upload/'; // 设置附件上传根目录
              $upload->saveName = 'time';
              // 上传单个文件
              //var_dump($_FILES);
              $info   =   $upload->uploadOne($_FILES['floorplanfn']);
              if(!$info) {// 上传错误提示错误信息
                  //$this->error($upload->getError());
              }else{// 上传成功 获取上传文件信息
                  //__ROOT__.'/attachments/vnet_'.$lasthistoryid.'/'.$now.'/111';
                  //echo $info['savepath'].'===='.$info['name'].'----'.$info['ext'];//文件扩展名
                  $date=date("Y-m-d");
                  $savename=time();
                  $floorplanfn='/upload/'.$date.'/'.$info['name'];//$info['name']
                  $savename='/upload/'.$date.'/'.$savename.'.'.$info['ext'];

              }

            //-------------------------------------------------------------
          }

          $editdata['name']= $name;
          //$editdata['floor']= $floor;
          $editdata['nodeid']= $contypeid;
          if (!empty($floorplanfn) && !empty($savename)){
            $editdata['floorplanfn']= $floorplanfn;
            $editdata['savename']= $savename;
          }

          //edit by zwj
          $Ipdb_locations->where('id="'.$id.'"')->setField($editdata);
          //$areadata['areaname']= $areaname;

          //edit by zwj
          //$Ipdb_locareas->where('locationid="'.$id.'"')->setField($areadata);

          $this->show('<script type="text/javascript" >alert("数据中心地点编辑成功");window.location.href="{:U("Ipdb/listlocations")}"; </script>');
          exit();     
    }


    $this->assign('url_flag','listlocations');
    $this->assign('id',$id);
    //$this->assign('floor',$floor);
    $this->assign('floorplanfn',$floorplanfn);
    $this->assign('savename',$savename);
    $this->assign('area',$area);
    $this->assign('contypeoption',$contypeoption);
    $this->assign('areaname',$areaname);
    $this->assign('nodeid',$nodeid);
    $this->assign('name',$name);
    $this->display();   
  } 



public function viewlocation(){

  if(!is_login()){
     $this->redirect("User/login");
  }    

  $uid        =   is_login();
  
  $Ipdb_locareas =M("ipdb_locareas");
  $Ipdb_locations =M("ipdb_locations");

  $locationid=$_REQUEST['locationid'];
  empty($locationid) && $this->error('请输入id');
  $locationlist=$Ipdb_locations->where('id="'.$locationid.'" ')->order('id desc')->select();
  $id=$locationlist[0]['id']; 
  $name=$locationlist[0]['name']; 
  $nodeid=$locationlist[0]['nodeid'];
  $area=$locationlist[0]['area'];

  $roomcount=$Ipdb_locareas->where('locationid="'.$id.'"')->count();
  $arealist=$Ipdb_locareas->where('locationid="'.$id.'"')->select();  


  $this->assign('url_flag','listlocations');
  $this->assign('list',$arealist);
  $this->assign('id',$id);
  $this->assign('locationid',$locationid);
  $this->assign('name',$name);
  $this->assign('area',$area);
  //$this->assign('floor',$floor);
  $this->assign('roomcount',$roomcount);
  $this->display();   
} 












  public function itemlocationslist(){

    if(!is_login()){
      $this->redirect("User/login");
    } 

    $id=$_REQUEST['id'];
    empty($id) && $this->error('请输入id');
    $Ipdb_items =M("ipdb_items");
    $Itemtype =M("itemtype");
    //$Ipdb_tag2software =M("ipdb_tag2software");
    $list=$Ipdb_items->where('locationid="'.$id.'"')->select();
    
    for($i=0;$i<count($list);$i++){

      $model=$list[$i]['model'];
      $itemtypeid=$list[$i]['itemtypeid'];

      $tagslist=$Itemtype->where(' id ="'.$itemtypeid.'"')->field('name')->select();
      $typedesc=$tagslist[0]['typedesc'];
      $list[$i]['typedesc']=$typedesc;

    }
    $this->assign('url_flag','listlocations');
    $this->assign('list',$list);
    $this->display();
  }







public function  dellocations(){

  if(!is_login()){
    $this->redirect("User/login");
  }    

  $uid        =   is_login();
  

  $Ipdb_locations =M("ipdb_locations");

  $id=I('get.id');
  empty($id) && $this->error('id不能为空'); 

  $Ipdb_locations->where('id="'.$id.'"')->delete();

  $this->show('<script type="text/javascript" >alert("机房删除成功");window.location.href="{:U("Ipdb/listlocations")}"; </script>'); 
  exit();
}




  public function listareas(){
 
    if(!is_login()){
       $this->redirect("User/login");
    }    

    $uid        =   is_login();
    

    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    $Ipdb_items =M("ipdb_items");

    $areaname= $_REQUEST['areaname'];
    if (empty($areaname)){
        $locareaslist=$Ipdb_locareas->order('id desc')->select();  
    }else{
        $locareaslist=$Ipdb_locareas->where('areaname like "%'.$areaname.'%"')->order('id desc')->select(); 
    }

    for($i=0;$i<count($locareaslist);$i++){
        $locationid=$locareaslist[$i]['locationid']; 
        $arealist=$Ipdb_locations->where('id="'.$locationid.'"')->select();
        $name=$arealist[0]['name'];
        $locareaslist[$i]['locname']=$name;

    }

    $this->assign('url_flag','listareas');
    $this->assign('list',$locareaslist);
    $this->assign('name',$name);
    $this->assign('areaname',$areaname);
    $this->display();   
  } 





  public function  itemareasadd(){
 
     if(!is_login()){
       $this->redirect("User/login");
     }    

    $uid        =   is_login();
    

    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    $Ipdb_node =M("ipdb_node");

    $locationoption="";
    $loclist=$Ipdb_locations->select();
    for($i=0;$i<count($loclist);$i++){
       $id=$loclist[$i]['id'];
       $name=$loclist[$i]['name'];
       $locationoption.='<option value="'.$id.'" >'.$name.'</option>';
    }


    
    $aDataList=$Ipdb_node->order(' id asc')->select();//where('contypeid=0')->

    $aDataList = $this->array_column($aDataList, null, 'id');

    $aTreeList = $this->getTreeList(0, $aDataList);//获取 下面 层次 用 getTreeList
    $contypeoption = $this->createOptions($aTreeList);


    

    if ( IS_POST ) {

          $contypeid= $_REQUEST['contypeid'];
          $areaname= $_REQUEST['areaname'];
          $locationid= $_REQUEST['locationid'];
          empty($locationid) && $this->error('请输入数据中心名称');
          empty($areaname) && $this->error('请输入房间名称');
          empty($contypeid) && $this->error('请输入标记');

          $arealist=$Ipdb_locareas->where('locationid="'.$locationid.'" and areaname="'.$areaname.'" ')->select();
          $id=$arealist[0]['id'];
          !empty($id) && $this->error('选择的数据中心房间名称重已存在!');

          $adddata['areaname']= $areaname;
          $adddata['locationid']= $locationid;
          $adddata['nodeid']= $contypeid;
          //insert by zwj
          
          if($Ipdb_locareas->add($adddata)){
              $this->show('<script type="text/javascript" >alert("房间添加成功");window.location.href="{:U("listareas")}"; </script>');
          }else{
              $this->show('<script type="text/javascript" >alert("房间添加失败");window.location.href="{:U("itemareasadd")}"; </script>');
          }
          exit();   

    }

    $this->assign('url_flag','listareas');
    $this->assign('list',$locationlist);
    $this->assign('contypeoption',$contypeoption);
    $this->assign('locationoption',$locationoption);
    $this->display();   
  }





  public function  itemareasedit(){
 
    if(!is_login()){
        $this->redirect("User/login");
    }    

    $uid        =   is_login();

    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    $Ipdb_node =M("ipdb_node");

    $id= $_REQUEST['id'];
    empty($id) && $this->error('ID参数为空!');
    $arealist=$Ipdb_locareas->where('id = "'.$id.'" ')->order('id desc')->select();
    $areaname=$arealist[0]['areaname'];
    $locationid=$arealist[0]['locationid'];
    $nodeid=$arealist[0]['nodeid'];

//---------------------------------------标记-------------------------------------------
    
    $aDataList=$Ipdb_node->order(' id asc')->select();//where('contypeid=0')->
    $aDataList = $this->array_column($aDataList, null, 'id');
    $aTreeList = $this->getTreeList(0, $aDataList);//获取 下面 层次 用 getTreeList
    $contypeoption = $this->createOptions($aTreeList,$nodeid);
//---------------------------------------标记-------------------------------------------

    $locationoption="";
    $loclist=$Ipdb_locations->select();
    for($i=0;$i<count($loclist);$i++){
       $tempid=$loclist[$i]['id'];
       $name=$loclist[$i]['name'];
       if($tempid==$locationid){
          $locationoption.='<option value="'.$tempid.'" selected="selected" >'.$name.'</option>';
       }else{
          $locationoption.='<option value="'.$tempid.'" >'.$name.'</option>';
       }
       
    }

    if ( IS_POST ) {

          $contypeid= $_REQUEST['contypeid'];
          $areaname= $_REQUEST['areaname'];
          $locationid= $_REQUEST['locationid'];
          $id= $_REQUEST['id'];
          empty($id) && $this->error('请输入ID');
          //empty($locationid) && $this->error('请输入数据中心名称');
          empty($areaname) && $this->error('请输入房间名称');
          empty($contypeid) && $this->error('请输入标记');

          $arealist=$Ipdb_locareas->where('locationid="'.$locationid.'" and areaname="'.$areaname.'" ')->select();
          $cid=$arealist[0]['id'];
          !empty($cid) && $this->error('选择的数据中心房间名称重已存在!');

          $editdata['areaname']= $areaname;
          //$editdata['locationid']= $locationid;
          $editdata['nodeid']= $contypeid;
          //edit by zwj
          $Ipdb_locareas->where('id="'.$id.'"')->setField($editdata);

          $this->show('<script type="text/javascript" >alert("房间编辑成功");window.location.href="{:U("listareas")}"; </script>');
          exit();   


    }

    $this->assign('url_flag','listareas');
    $this->assign('id',$id); 
    $this->assign('areaname',$areaname); 
    $this->assign('list',$locationlist);
    $this->assign('contypeoption',$contypeoption);
    $this->assign('locationoption',$locationoption);
    $this->display();   
  }



  public function itemviewareas(){

    if(!is_login()){
      $this->redirect("User/login");
    } 

    $locationid=$_REQUEST['id'];
    empty($locationid) && $this->error('请输入id');
    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    //$Ipdb_tag2software =M("ipdb_tag2software");
    $list=$Ipdb_locareas->where('locationid="'.$locationid.'"')->select();
    
    for($i=0;$i<count($list);$i++){

      $templocationid=$list[$i]['locationid'];
      $loclist=$Ipdb_locations->where(' id ="'.$templocationid.'"')->field('name')->select();
      $name=$loclist[0]['name'];

      $list[$i]['locname']=$name;

    }
    
    $this->assign('url_flag','listareas');
    $this->assign('list',$list);
    $this->display();
  }



  public function viewarea(){
 
    if(!is_login()){
        $this->redirect("User/login");
    }    

    $uid        =   is_login();
    

    $Ipdb_racks =M("ipdb_racks");
    $Ipdb_locareas =M("ipdb_locareas");
    $Ipdb_locations =M("ipdb_locations");
    $Ipdb_items =M("ipdb_items");

    $areaid=$_REQUEST['areaid'];
    empty($areaid) && $this->error('请输入id');
    $arealist=$Ipdb_locareas->where('id="'.$areaid.'" ')->order('id desc')->select();
    $id=$arealist[0]['id']; 
    $areaname=$arealist[0]['areaname']; 
    $locationid=$arealist[0]['locationid'];

    $loclist=$Ipdb_locations->where('id="'.$locationid.'"')->select();  
    $name=$loclist[0]['name'];
    $floor=$loclist[0]['floor'];
    $floor=$floor."层";

    $rackcount=$Ipdb_racks->where('locareaid="'.$id.'"')->count();
    $racklist=$Ipdb_racks->where('locareaid="'.$id.'" ')->select();
    $tempracklist=$Ipdb_racks->where('locareaid="'.$id.'" ')->field(' sum(usize) as allusize')->select();
    $unitscount=$tempracklist[0]['allusize'];
    $temprackids='';
    for($i=0;$i<count($racklist);$i++){
      $temprackid=$racklist[$i]['id'];
      $temprackids=$temprackids.','.$temprackid;
    }
    $temprackids=substr($temprackids,1,strlen($temprackids));

    if (strlen($temprackids)>0){
        $tiemlist=$Ipdb_items->where('rackid in ('.$temprackids.') ')->field(' sum(usize) as usedusize')->select();
        $usedusize=$tiemlist[0]['usedusize'];
    }
    $usedusize=($usedusize/$unitscount)*100;
    $usedusize=round($usedusize);

    
    $this->assign('url_flag','listareas');
    $this->assign('list',$arealist);
    $this->assign('racklist',$racklist);
    $this->assign('id',$id);
    $this->assign('areaid',$areaid);
    $this->assign('name',$name);
    $this->assign('areaname',$areaname);
    $this->assign('area',$area);
    $this->assign('floor',$floor);
    $this->assign('rackcount',$rackcount);
    $this->assign('unitscount',$unitscount);
    $this->assign('usedusize',$usedusize);
    $this->display();   
  } 











public function  delareas(){

  if(!is_login()){
    $this->redirect("User/login");
  }    

  $uid        =   is_login();
  

  $Ipdb_locareas =M("ipdb_locareas");

  $id=I('get.id');
  empty($id) && $this->error('id不能为空'); 

  $Ipdb_locareas->where('id="'.$id.'"')->delete();
  $this->show('<script type="text/javascript" >alert("房间删除成功");window.location.href="{:U("Ipdb/listareas")}"; </script>'); 
  exit();
}






public function locareas(){

 
    if(!is_login()){
      $this->redirect("User/login");
    }    

    $uid        =   is_login();
    

    $id=$_REQUEST['id'];
    empty($id) && $this->error('参数不能为空');
    $Ipdb_locareas =M("ipdb_locareas");

    $itemtaglist=$Ipdb_tags->where('id = "'.$id.'" ')->select();
    $id=$itemtaglist[0]['id']; 
    $name=$itemtaglist[0]['name']; 

    if ( IS_POST ) {

      $id=$_REQUEST['id'];
      empty($id) && $this->error('参数不能为空'); 
      $data['name']= I('post.name');

      $Ipdb_tags->where('id="'.$id.'"')->setField($data);
      $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("Ipdb/itemtagtypes")}"; </script>');
      exit();

    }


    $this->assign('url_flag','listlocations');
    $this->assign('id',$id);
    $this->assign('name',$name);
    $this->display();
}







  public function  department(){
 
    if(!is_login()){
      $this->redirect("User/login");
    }    

     $uid        =   is_login();
     

    $Department =M("department");

    $name=$_REQUEST['name'];
    if (!empty($name)){
        $departmentlist=$Department->where('name like "%'.$name.'%"')->order('id desc')->select();
    }else{
        $departmentlist=$Department->order('id desc')->select();
    }


/*    if ( IS_POST ) {

      $adddata['name']= $_REQUEST['name'];
      $Ipdb_tags->data($adddata)->add();
      $this->show('<script type="text/javascript" >alert("标记添加成功");window.location.href="{:U("Ipdb/itemtagtypes")}"; </script>');
      exit();

    }*/

    $this->assign('url_flag','listlocations');

    $this->assign('list',$departmentlist);
    $this->display();   
  } 





public function  departmentaccess(){
 
  if(!is_login()){
    $this->redirect("User/login");
  }    
  $uid        =   is_login();
  
  $Department = M('department');
  $Ipdb_menu = M('ipdb_menu_zichan');

  $id=$_REQUEST['id'];
  empty($id) && $this->error('id不能为空');

  $departmentoption="";
  $departmentlist=$Department->order(' id desc')->select();

  for($i=0;$i<count($departmentlist);$i++){
     $did=$departmentlist[$i]['id'];
     $dname=$departmentlist[$i]['name'];

     if ($id==$did){
          $departmentoption.='<option value="'.__ROOT__.'/index.php?s=/Home/Ipdb/departmentaccess/id/'.$did.'.html" selected="selected" >'.$dname.'</option>';
     }else{
          $departmentoption.='<option value="'.__ROOT__.'/index.php?s=/Home/Ipdb/departmentaccess/id/'.$did.'.html"  >'.$dname.'</option>';
     }


  }

if(IS_POST){
  
  $id=$_REQUEST['id'];
  empty($id) && $this->error('id不能为空');
  $rules=I('post.rules');
  $data['zichan_rules']=implode(",",$rules);

  $list=$Department->where('id="'.$id.'"')->setField($data);
  if($list){
      $this->show('<script type="text/javascript" >alert("设置成功！");window.location.href="{:U(\"departmentaccess?id='.$id.'\")}"; </script>');
      //$this->success('设置成功！',U("usergroupaccess?id=".$id));
      exit();
  }else{
      $this->show('<script type="text/javascript" >alert("设置失败！");window.location.href="{:U(\"departmentaccess?id='.$id.'\")}"; </script>');
      //$this->success('设置失败！',U("usergroupaccess?id=".$id));
      exit();
  }

}



//--------------------------------权限动态展现-------------------------------------
$checkmod='';

$menulist=$Ipdb_menu->where('pid=0')->order(' sort asc')->select();

for($i=0;$i<count($menulist);$i++){
   $menuid=$menulist[$i]['id'];
   $title=$menulist[$i]['title'];

//------------------------------------权限遍历确认开始------------------------------------------
    $tempusergrouplist=$Department->where('id="'.$id.'"')->select();
    unset($rules);
    $rules=$tempusergrouplist[0]['zichan_rules'];
    $rulearray=explode(',',$rules);

    if(in_array($menuid,$rulearray)){$checked='checked';}else{$checked='';}
    //echo $checked."<br />";

//----------------------------------一级菜单开始----------------------------------------   
   $checkmod.='<div class="form-horizontal form-horizontal1 auth-form coco-form">';
   $checkmod.='<dl class="checkmod"><dt class="hd"><label class="checkbox">';
   $checkmod.='<input class="auth_rules rules_all" type="checkbox" name="rules[]" value="'.$menuid.'" '.$checked.'>';
   $checkmod.=$title;
   $checkmod.='</label></dt>';


  //----------------------------------二级菜单开始----------------------------------------
     $checkmod.='<dd class="bd">';

     $submenulist=$Ipdb_menu->where('pid="'.$menuid.'"')->select();
     for($ii=0;$ii<count($submenulist);$ii++){
         $submenuid=$submenulist[$ii]['id'];
         $submenutitle=$submenulist[$ii]['title'];
         if(in_array($submenuid,$rulearray)){$checked='checked';}else{$checked='';}
        
         $checkmod.='<div class="rule_check">';

         $checkmod.='<div >';
         $checkmod.='<label class="checkbox">';
         $checkmod.='<input class="auth_rules rules_row" type="checkbox" name="rules[]" value="'.$submenuid.'" '.$checked.' >';
         $checkmod.=$submenutitle;
         $checkmod.='</label>';
         $checkmod.='</div>';

         $checkmod.='<span class="divsion">&nbsp;</span><span class="child_row">';
         $threemenulist=$Ipdb_menu->where('pid="'.$submenuid.'"')->select();
         for($iii=0;$iii<count($threemenulist);$iii++){
             $threemenuid=$threemenulist[$iii]['id'];
             $threemenutitle=$threemenulist[$iii]['title'];
             if(in_array($threemenuid,$rulearray)){$checked='checked';}else{$checked='';}

             $checkmod.='<label class="checkbox">';
             $checkmod.='<input class="auth_rules" type="checkbox" name="rules[]" value="'.$threemenuid.'" '.$checked.' >';
             $checkmod.=$threemenutitle;
         }
         $checkmod.='</span>';


         $checkmod.='</div>';
         

     }
     $checkmod.='</dd>';
  //----------------------------------二级菜单结束----------------------------------------
        
     $checkmod.='</dl>';
   $checkmod.='</div>';
//----------------------------------一级菜单结束----------------------------------------

//------------------------------------权限遍历确认结束------------------------------------------
            

}



//--------------------------------权限动态展现结束-------------------------------------


/*
        <div class="form-horizontal form-horizontal1 auth-form coco-form">
  
            <dl class="checkmod">
                <dt class="hd">
                    <label class="checkbox"><input class="auth_rules rules_all" type="checkbox" name="rules[]" value="1">一级</label>
                </dt>

            <dd class="bd">

                <div class="rule_check">
                    <div>
                        <label class="checkbox"><input class="auth_rules rules_row" type="checkbox" name="rules[]" value="2">二级</label>
                    </div>

                    <span class="divsion">&nbsp;</span>
                        <span class="child_row">
                            <label class="checkbox"><input class="auth_rules" type="checkbox" name="rules[]" value="74">三级</label>
                        </span>
                </div>

                <div class="rule_check">

                        <div>
                            <label class="checkbox"><input class="auth_rules rules_row" type="checkbox" name="rules[]" value="4">当前告警</label>
                        </div>

                        <span class="divsion">&nbsp;</span>
                            <span class="child_row">
                                <label class="checkbox">
                                    <input class="auth_rules" type="checkbox" name="rules[]" value="5">资产查看<label class="checkbox">
                                    <input class="auth_rules" type="checkbox" name="rules[]" value="73">未处理</label>
                                </label>
                            </span>
                </div>

                </div>

            </dd>


           </dl>  


        </div>

*/











  $this->assign('url_flag','listlocations');
  $this->assign('id',$id);
  $this->assign('uid',$uid);
  $this->assign('checkmod',$checkmod);
  $this->assign('nickname',$nickname);
  $this->assign('departmentoption',$departmentoption); 
  $this->display();  

}








public function departmentaccesspost(){

  if(!is_login()){
    $this->redirect("User/login");
  }  
  $uid        =   is_login();
  
  $Department = M('department');


  $id=$_REQUEST['id'];
  empty($id) && $this->error('id不能为空');
  $rules=I('post.rules');
  $data['rules']=implode(",",$rules);

  $list=$Department->where('id="'.$id.'"')->setField($data);
  if($list){
      $this->show('<script type="text/javascript" >alert("设置成功！");window.location.href="{:U(\"departmentaccess?id='.$id.'\")}"; </script>');
      //$this->success('设置成功！',U("usergroupaccess?id=".$id));
      exit();
  }else{
      $this->show('<script type="text/javascript" >alert("设置失败！");window.location.href="{:U(\"departmentaccess?id='.$id.'\")}"; </script>');
      //$this->success('设置失败！',U("usergroupaccess?id=".$id));
      exit();
  }


}




public function  dellocimg(){

  if(!is_login()){
    $this->redirect("User/login");
  }    

  $uid        =   is_login();
  $nickname = M('user')->getFieldByUid($uid, 'username');

  $Ipdb_locations=M('ipdb_locations');

  $id=I('get.id');
  empty($id) && $this->error('id不能为空'); 

  $editdata['floorplanfn']='';
  $editdata['savename']='';
  $Ipdb_locations->where('id="'.$id.'"')->setField($editdata);
  $this->show('<script type="text/javascript" >alert("图片删除成功");window.location.href="index.php?s=/Home/Ipdb/editlocation/id/'.$id.'.html"; </script>');
  exit();


}







}


?>

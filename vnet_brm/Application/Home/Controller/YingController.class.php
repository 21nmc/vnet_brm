<?php
namespace Home\Controller;
use Think\Controller;
class  YingController extends HomeController {
    public function index(){
        
        
        $this->display();
    }
    
    public function itemtypes() {
        $Itemtype = M('itemtype');
        $list=$Itemtype->order('id asc')->select();
        $this->assign('list',$list);
        $this->assign('url_flag','lei_index'); //left flag
        $this->display();
    }
    
    public function itemtype_new() {
        if ( IS_POST ) {
            $Itemtype = M('itemtype');
        
            $typedesc= I('post.typedesc');
            $hassoftware= I('post.hassoftware');
            $itemid= I('post.itemid');
           
        
            $data['typedesc'] = $typedesc;
            $data['hassoftware'] = $hassoftware;
            $data['itemid'] = $itemid;
            
            empty($typedesc) && $this->error('请输入类型描述:');
        
            $checklist=$Itemtype->where('typedesc = "'.$typedesc.'" ')->select();
            if (empty($checklist)){
                $list=$Itemtype->data($data)->add();
                if (!empty($list)){
                    //echo "资产添加成功";
                    $this->show('<script type="text/javascript" >alert("类型添加成功");window.location.href="{:U("Ying/itemtypes")}"; </script>');
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("类型重复添加，请重新添加");window.location.href="{:U("Ying/itemtype_new")}"; </script>');
            }
        }
        $this->assign('url_flag','lei_index'); //left flag
        $this->display();
        
    }
    
    
    //资产类型_ EDIT
    public function itemtype_edit(){
        $Itemtype = M('itemtype');
        $id   =  $_REQUEST['id'];
       
        $list=$Itemtype->where('id = "'.$id.'" ')->select();
    
        $typedesc=$list[0]['typedesc'];
        $hassoftware=$list[0]['hassoftware'];
        $typeid=$list[0]['typeid'];
        
        
        if ( IS_POST ) {
            $typedesc= I('post.typedesc');
            $hassoftware= I('post.hassoftware');
            $itemid= I('post.itemid');
    
            $data['typedesc'] = $typedesc;
            $data['hassoftware'] = $hassoftware;
            $data['itemid'] = $itemid;
          
    
            empty($typedesc) && $this->error('请输入类型描述:');
            
    
            $list=$Itemtype->where('id="'.$id.'" ')->setField(array('typedesc'=>$typedesc,'hassoftware'=>$hassoftware,'typeid'=>$typeid));
            if ($list==1){
                $this->show('<script type="text/javascript" >alert("类型修改成功");window.location.href="{:U("Ying/itemtypes")}"; </script>');
                exit();
            }
        }
        $this->assign('typedesc',$typedesc);
        $this->assign('hassoftware',$hassoftware);
        $this->assign('typeid',$typeid);
        $this->assign('url_flag','lei_index'); //left flag
        $this->display();
    
    }
    
    public function itemtype_del() {
        $id = $_REQUEST['id'];
        M('itemtype')->where("id = $id")->delete();
        $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Ying/itemtypes")}"; </script>');die;
    
    }
    
    
}
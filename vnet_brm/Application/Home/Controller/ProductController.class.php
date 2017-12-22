<?php
namespace Home\Controller;
use Think\Controller;
class  ProductController extends HomeController {
    public function index(){
        
        $this->display();
    }
    
    public function productlist() {

      if(!is_login()){
        $this->redirect("User/login");
      }   

        $Product = M('product');
        $list=$Product->order('id asc')->select();
        $this->assign('list',$list);
        $this->assign('url_flag','lei_index'); //left flag
        $this->display();
    }
    
    public function product_add() {

      if(!is_login()){
        $this->redirect("User/login");
      }   

        if ( IS_POST ) {
            $Product = M('product');
            $title= I('post.title');
            empty($title) && $this->error('请输入产品名称:');
        
            $adddata['title'] = $title;

            $plist=$Product->where('title = "'.$title.'" ')->select();
            if (empty($plist)){
                $list=$Product->data($adddata)->add();
                if (!empty($list)){
                    $this->show('<script type="text/javascript" >alert("产品添加成功");window.location.href="{:U("Product/productlist")}"; </script>');
                }
            }else{
                $this->show('<script type="text/javascript" >alert("产品重复添加，请重新添加");window.location.href="{:U("Product/product_add")}"; </script>');
            }
            exit();

        }
        $this->assign('url_flag','lei_index'); //left flag
        $this->display();
        
    }
    
    
//资产类型_ EDIT
public function product_edit(){

    if(!is_login()){
        $this->redirect("User/login");
    }    

    $uid = is_login();

    $Product = M('product');
    $id   =  $_REQUEST['id'];
    empty($id) && $this->error('产品ID不能为空');

    $list=$Product->where('id = "'.$id.'" ')->select();
    $title=$list[0]['title'];

    if ( IS_POST ) {
        $id= I('post.id');
        $title= I('post.title');
        empty($id) && $this->error('ID不能为空');
        empty($title) && $this->error('请输入产品名称');
        $editdata['title'] = $title;


        $plist=$Product->where('title = "'.$title.'" ')->select();
        if (empty($plist)){
            $list=$Product->where('id="'.$id.'" ')->setField(array('title'=>$title));
            if ($list==1){
                $this->show('<script type="text/javascript" >alert("产品修改成功");window.location.href="{:U("Product/productlist")}"; </script>');
                exit();
            }
        }else{
            $this->show('<script type="text/javascript" >alert("产品重名");window.location.href="{:U(\"Product/product_edit?id='.$id.'\")}"; </script>');
        }
        exit();


    }
    $this->assign('id',$id);
    $this->assign('title',$title);
    $this->assign('url_flag','lei_index'); //left flag
    $this->display();
    
}




public function  delproduct(){

  if(!is_login()){
    $this->redirect("User/login");
  }    

  $uid        =   is_login();

  $Product = M('product');
  $id   =  $_REQUEST['id'];
  empty($id) && $this->error('产品ID不能为空');

  $Product->where('id="'.$id.'"')->delete();
  $this->show('<script type="text/javascript" >alert("产品删除成功");window.location.href="{:U("Product/productlist")}"; </script>'); 
  exit();

}




    
    
}
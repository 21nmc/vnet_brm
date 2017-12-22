<?php
namespace Home\Controller;
use Think\Controller;
class  IpController extends HomeController {
    
    public function index(){
        
    	$this->assign('url_flag','ip');
        $this->display();
    }
    
   
    

}
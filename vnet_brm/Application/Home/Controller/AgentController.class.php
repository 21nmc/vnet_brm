<?php
namespace Home\Controller;
use Think\Controller;
class  AgentController extends HomeController {
    public function index(){
        
        $Agents = M('agents');
        $list=$Agents->order('id asc')->select();
        $this->assign('list',$list);
        $this->assign('url_flag','lei_index'); //left flag
        $this->display();
    }
    
    public function agent_new() {
        if ( IS_POST ) {
            $Agents = M('agents');
    
            $type= I('post.type');
            $title= I('post.title');
            $contactinfo= I('post.contactinfo');
            $contacts= I('post.contacts');
            $urls= I('post.urls');
             
    
            $data['type'] = $type;
            $data['title'] = $title;
            $data['contactinfo'] = $contactinfo;
            $data['contacts'] = $contacts;
            $data['urls'] = $urls;
    
            empty($title) && $this->error('请输入标题:');
    
            $checklist=$Agents->where('title = "'.$title.'" ')->select();
            if (empty($checklist)){
                $list=$Agents->data($data)->add();
                if (!empty($list)){
                    //echo "资产添加成功";
                    $this->show('<script type="text/javascript" >alert("代理添加成功");window.location.href="{:U("Agent/index")}"; </script>');
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("代理重复添加，请重新添加");window.location.href="{:U("Agent/agent_new")}"; </script>');
            }
        }
        $this->assign('url_flag','lei_index'); //left flag
        $this->display();
    }

    
}
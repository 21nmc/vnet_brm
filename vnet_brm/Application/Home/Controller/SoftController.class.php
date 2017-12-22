<?php
namespace Home\Controller;
use Think\Controller;
class  SoftController extends HomeController {
    
    public function index(){
        
        $itemType = M('itemtype');
        $Demo = M('software');
        $Agents = M('agents');
        $agent_list = $Agents->select();
        $agent_list = deal_array($agent_list,'id','title');
        
        $condition = array();
        if ($_GET['itemtypeid']) {
            $condition['itemtypeid'] = $_GET['itemtypeid'];
        }
        if ($_GET['stitle']) {
            $condition['stitle'] = array('like',"%".$_GET['stitle']."%");
        }
        
        $count = $Demo->where($condition)->field("id")->count();
        $page = new \Think\Page($count,10);
        $list=$Demo->where($condition)->order('id desc')->limit($page->firstRow,$page->listRows)->select();
        
        $map['itemtypeid'] = $_GET['itemtypeid'];
        $map['stitle'] = $_GET['stitle'];
        foreach($map as $key=>$val) {
            $p->parameter .= "$key=".urlencode($val)."&";
        }
        
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        
        $this->assign('page',$page->show());
        
        $type_all_list = $itemType->select();
        
        $this->assign('type_all_list',$type_all_list);
        $this->assign('list',$list);
        $this->assign('agent_list',$agent_list);
        $this->assign('url_flag','soft_index');
        $this->display();
    }
    
    //资产_ new
    public function soft_new(){
        if ( IS_POST ) {
            $Demo = M('software');
        
            $manufacturerid= I('post.manufacturerid');
            $stitle= I('post.stitle');
            $purchdate= I('post.purchdate');
            $sversion= I('post.sversion');
            $licqty= I('post.licqty');
            $lictype= I('post.lictype');
            $slicenseinfo= I('post.slicenseinfo');
            $sinfo= I('post.sinfo');
            $itemtypeid= I('post.itemtypeid');
        
            $data['manufacturerid'] = $manufacturerid;
            $data['stitle'] = $stitle;
            $data['sversion'] = $sversion;
            $data['purchdate'] = strtotime($purchdate);
            $data['licqty'] = $licqty;
            $data['lictype'] = $lictype;
            $data['slicenseinfo'] = $slicenseinfo;
            $data['sinfo'] = $sinfo;
            $data['itemtypeid'] = $itemtypeid;
         
        
            empty($stitle) && $this->error('请输入SN号:');
        
            $checklist=$Demo->where('stitle = "'.$stitle.'" ')->select();
            if (empty($checklist)){
                $list=$Demo->data($data)->add();
                if (!empty($list)){
                    //echo "资产添加成功";
                    $this->show('<script type="text/javascript" >alert("软件添加成功");window.location.href="{:U("Soft/index")}"; </script>');
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("软件重复添加，请重新添加");window.location.href="{:U("Soft/soft_new")}"; </script>');
            }
        }
        
        $itemType = M('itemtype');
        $type_all_list = $itemType->select();
        
        $agent_list = M('agents')->where(" type = 2 ")->select();
        $this->assign('agent_list',$agent_list);
        $this->assign('type_all_list',$type_all_list);
        $this->assign('url_flag','soft_index'); //left flag
        $this->display();
    }
    
    
    public function soft_edit() {
        
        $id = $_REQUEST['id'];
        $flag = $_REQUEST['flag'];
        
        $Items = M('ipdb_items');
        $Softs = M('software');
        $contract = M('contracts');
        
        if (IS_POST) {
            if ($flag == '1') {
                $manufacturerid= I('post.manufacturerid');
                $stitle= I('post.stitle');
                $purchdate= strtotime(I('post.purchdate'));
                $sversion= I('post.sversion');
                $licqty= I('post.licqty');
                $lictype= I('post.lictype');
                $slicenseinfo= I('post.slicenseinfo');
                $sinfo= I('post.sinfo');
                $itemtypeid= I('post.itemtypeid');
                
                empty($stitle) && $this->error('请输入SN号:');
                
                $list=$Softs->where('id="'.$id.'"  ')->setField(array('manufacturerid'=>$manufacturerid,'stitle'=>$stitle,'purchdate'=>$purchdate,'sversion'=>$sversion,'licqty'=>$licqty,'lictype'=>$lictype,'slicenseinfo'=>$slicenseinfo,'sinfo'=>$sinfo,'itemtypeid'=>$itemtypeid));
                if ($list==1){
                    $url = U("Soft/soft_edit?id=".$id);
                    $this->show('<script type="text/javascript" >alert("基本信息修改成功");window.location.href="{$url}"; </script>');
                    exit();
                }
                
            }else if($flag == '2'){
                 //硬件关联
                 $itemids = I('post.itemid');
                 //$link = M('itemlink');
                 $item2soft = M('item2soft');
                 $del = $item2soft->where("softid = $id")->delete();
                 
                 foreach ($itemids as $it){
                     $checklist=$item2soft->where("itemid = $it and softid=$id")->select();
                     if (empty($checklist)){
                         $data['softid'] = $id;
                         $data['itemid'] = $it;
                         $ins= $item2soft->data($data)->add();
                     }
                 }
                 $url = U("Soft/soft_edit?id=".$id);
                 $this->show('<script type="text/javascript" >alert("关联硬件修改成功");window.location.href="{$url}"; </script>');
                 exit();
             }else if($flag == '3'){
                 //合同关联
                 $contractids = I('post.contract_id');
                 $contract2soft = M('contract2soft');
                 $del = $contract2soft->where("softid = $id")->delete();
                  
                 foreach ($contractids as $contract_one){
                     $checklist=$contract2soft->where("softid = $id and contractid = $contract_one")->select();
                     if (empty($checklist)){
                         $data['softid'] = $id;
                         $data['contractid'] = $contract_one;
                         $ins= $contract2soft->data($data)->add();
                     }
                 }
                 $url = U("Soft/soft_edit?id=".$id);
                 $this->show('<script type="text/javascript" >alert("关联合同修改成功");window.location.href="{$url}"; </script>');
                 exit();
             }else{
                 if($_FILES['imageurl']['error'] == 0)
                 {
                     $upload = new \Think\Upload();// 实例化上传类
                     $upload->maxSize = 50 * 1024 * 1024 ; // 1M
                     $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                     $upload->rootPath = './upload/'; // 设置附件上传根目录
                     $upload->savePath = 'softs/'; // 设置附件上传（子）目录
                     $upload->saveRule="";
                     // 上传文件
                     $info   =   $upload->upload();
                     if(!$info)
                     {
                         // 获取失败原因把错误信息保存到 模型的error属性中，然后在控制器里会调用$model->getError()获取到错误信息并由控制器打印
                         $this->error = $upload->getError();
                         return FALSE;
                     }
                     else
                     {
                         /**************** 生成缩略图 *****************/
                         // 先拼成原图上的路径
                          
                         $logo = $info['imageurl']['savepath'] . $info['imageurl']['savename'];
                 
                         /**************** 把路径放到表单中 *****************/
                 
                         $list=$Softs->where('id="'.$id.'"  ')->setField(array('imageurl'=>$logo));
                         $url = U("Soft/soft_edit?id=".$id);
                         $this->show('<script type="text/javascript" >alert("图片修改成功");window.location.href="{$url}"; </script>');
                         exit();
                     }
                 }
             }
        }
        
        $item_list = $Items->select();
        $contract_list = $contract->select();
        $soft_info = $Softs->where('id="'.$id.'"  ')->find();
        $agent_list = M('agents')->where(" type = 2 ")->select();
        
        /**soft2item*/
        $itemlink_list = M('item2soft')->field('itemid')->where("softid=$id")->select();
        $itemlink_list_temp = array();
        if (!empty($itemlink_list)) {
            foreach ($itemlink_list as $vo){
                $itemlink_list_temp[] = $vo['itemid'];
            }
        }
        
        /**contractlink**/
        $contractlink_list = M('contract2soft')->field('contractid')->where("softid=$id")->select();
        $contractlink_list_temp = array();
        if (!empty($contractlink_list)) {
            foreach ($contractlink_list as $vo){
                $contractlink_list_temp[] = $vo['contractid'];
            }
        }
        $itemType = M('itemtype');
        $type_all_list = $itemType->select();
        
        $this->assign('type_all_list',$type_all_list);
        $this->assign('itemlink_list',$itemlink_list_temp);// 关联硬件传值
        $this->assign('contractlink_list',$contractlink_list_temp);// 关联合同传值
        $this->assign('agent_list',$agent_list);
        $this->assign('soft_info',$soft_info);
        $this->assign('item_list',$item_list); //所有硬件
        $this->assign('contract_list',$contract_list); //所有合同
        $this->assign('url_flag','soft_index'); //left flag
        $this->display();
    }
    
    /**软件删除**/
    public function soft_del(){
        $soft = M('software');
        $id = $_REQUEST['id'];
    
        $where_soft['id'] = $id;
        $del = $soft->where($where_soft)->delete();
        if ($del) {
            
            $del_item_link = M('item2soft')->where("softid = $id or itemid = $id")->delete();
            $del_contract_link = M('contract2soft')->where("softid = $id")->delete();

            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Soft/index")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Soft/index")}"; </script>');die;
        }
    }
    
    
    
    public function contract_index(){
        
        $Demo = M('contracts');
        $list = $Demo->order('id asc')->select();
        $type_list = M('ipdb_contracttypes')->select();
        $type_list = deal_array($type_list,'id','name');
        
        $this->assign('type_list',$type_list);
        $this->assign('list',$list);
        $this->assign('url_flag','contract_index'); //left flag
        $this->display();
    }
    
    public function contract_new() {
        
        
        if ( IS_POST ) {
            var_dump($_POST);die;
            $Demo = M('contracts');
        
            $title= I('post.title');
            $number= I('post.number');
            $type= I('post.type');
            $parentid= I('post.parentid');
            $totalcost= I('post.totalcost');
            $startdate= I('post.startdate');
            $currentenddate= I('post.currentenddate');
            $description= I('post.description');
            $comments= I('post.comments');
           
        
            $data['title'] = $title;
            $data['number'] = $number;
            $data['type'] = $type;
            $data['parentid'] = $parentid;
            $data['totalcost'] = $totalcost;
            $data['startdate'] = strtotime($startdate);
            $data['currentenddate'] = strtotime($currentenddate);
            $data['description'] = $description;
            $data['comments'] = $comments;
            
            empty($title) && $this->error('请输入类型描述:');
        
            $checklist=$Demo->where('title = "'.$title.'" ')->select();
            if (empty($checklist)){
                $list=$Demo->data($data)->add();
                if (!empty($list)){
                    $this->show('<script type="text/javascript" >alert("合同添加成功");window.location.href="{:U("Soft/contract_index")}"; </script>');
                    exit();
                }
            }else{
                $this->show('<script type="text/javascript" >alert("合同重复添加，请重新添加");window.location.href="{:U("Soft/contract_new")}"; </script>');
            }
        }
        
        $type_list = M('ipdb_contracttypes')->select();
        $contract_list = M('contracts')->select();
        
        
        $this->assign('type_list',$type_list);
        $this->assign('contract_list',$contract_list);
        $this->assign('url_flag','contract_index'); //left flag
        $this->display();
    }
    
    public function contract_edit() {
        $id = $_REQUEST['id'];
        $flag = $_REQUEST['flag'];
        
        $Items = M('ipdb_items');
        $Softs = M('software');
        $contract = M('contracts');
        
        if (IS_POST) {
            if ($flag == '1') {
                $title= I('post.title');
                $number= I('post.number');
                $type= I('post.type');
                $parentid= I('post.parentid');
                $totalcost= I('post.totalcost');
                $startdate= strtotime(I('post.startdate'));
                $currentenddate= strtotime(I('post.currentenddate'));
                $description= I('post.description');
                $comments= I('post.comments');
        
                empty($title) && $this->error('请输入SN号:');
        
                $list=$contract->where('id="'.$id.'"  ')->setField(array('title'=>$title,'number'=>$number,'type'=>$type,'parentid'=>$parentid,'totalcost'=>$totalcost,'startdate'=>$startdate,'currentenddate'=>$currentenddate,'description'=>$description,'comments'=>$comments));
                if ($list==1){
                    $url = U("Soft/contract_edit?id=".$id);
                    $this->show('<script type="text/javascript" >alert("基本信息修改成功");window.location.href="{$url}"; </script>');
                    exit();
                }
        
            }else if($flag == '2'){
                //硬件关联
                $itemids = I('post.itemid');
                //$link = M('itemlink');
                $contract2item = M('contract2item');
                $del = $contract2item->where("contractid = $id")->delete();
                 
                foreach ($itemids as $it){
                    $checklist=$contract2item->where("itemid = $it and contractid=$id")->select();
                    if (empty($checklist)){
                        $data['contractid'] = $id;
                        $data['itemid'] = $it;
                        $ins= $contract2item->data($data)->add();
                    }
                }
                $url = U("Soft/contract_edit?id=".$id);
                $this->show('<script type="text/javascript" >alert("关联硬件修改成功");window.location.href="{$url}"; </script>');
                exit();
            }else if($flag == '3'){
                //合同软件
                $softids = I('post.softid');
                $contract2soft = M('contract2soft');
                $del = $contract2soft->where("contractid = $id")->delete();
        
                foreach ($softids as $soft_one){
                    $checklist=$contract2soft->where("softid = $soft_one and contractid = $id")->select();
                    if (empty($checklist)){
                        $data['softid'] = $soft_one;
                        $data['contractid'] = $id;
                        $ins= $contract2soft->data($data)->add();
                    }
                }
                $url = U("Soft/contract_edit?id=".$id);
                $this->show('<script type="text/javascript" >alert("关联合同修改成功");window.location.href="{$url}"; </script>');
                exit();
            }else{
                if($_FILES['imageurl']['error'] == 0)
                {
                    $upload = new \Think\Upload();// 实例化上传类
                    $upload->maxSize = 50 * 1024 * 1024 ; // 1M
                    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                    $upload->rootPath = './upload/'; // 设置附件上传根目录
                    $upload->savePath = 'contracts/'; // 设置附件上传（子）目录
                    $upload->saveRule="";
                    // 上传文件
                    $info   =   $upload->upload();
                    if(!$info)
                    {
                        // 获取失败原因把错误信息保存到 模型的error属性中，然后在控制器里会调用$model->getError()获取到错误信息并由控制器打印
                        $this->error = $upload->getError();
                        return FALSE;
                    }
                    else
                    {
                        /**************** 生成缩略图 *****************/
                        // 先拼成原图上的路径
        
                        $logo = $info['imageurl']['savepath'] . $info['imageurl']['savename'];
                         
                        /**************** 把路径放到表单中 *****************/
                         
                        $list=$contract->where('id="'.$id.'"  ')->setField(array('imageurl'=>$logo));
                         $url = U("Soft/contract_edit?id=".$id);
                        $this->show('<script type="text/javascript" >alert("图片修改成功");window.location.href="{$url}"; </script>');
                        exit();
                    }
                }
            }
        }
        
        $item_list = $Items->select();
        $soft_list = $Softs->select();
        $contract_list = $contract->where("id != $id")->select();
        $contract_info = $contract->where('id="'.$id.'"  ')->find();
        $type_list = M('ipdb_contracttypes')->select();
        
        /**contract2item*/
        $itemlink_list = M('contract2item')->field('itemid')->where("contractid=$id")->select();
        $itemlink_list_temp = array();
        if (!empty($itemlink_list)) {
            foreach ($itemlink_list as $vo){
                $itemlink_list_temp[] = $vo['itemid'];
            }
        }
        
        /**softlink**/
        $softlink_list = M('contract2soft')->field('softid')->where("contractid=$id")->select();
        $softlink_list_temp = array();
        if (!empty($softlink_list)) {
            foreach ($softlink_list as $vo){
                $softlink_list_temp[] = $vo['softid'];
            }
        }
        
        
        $this->assign('itemlink_list',$itemlink_list_temp);// 关联硬件传值
        $this->assign('softlink_list',$softlink_list_temp);// 关联软件传值
        $this->assign('contract_list',$contract_list); //除自己外的所有合同
        $this->assign('contract_info',$contract_info);
        
        $this->assign('item_list',$item_list); //所有硬件
        $this->assign('soft_list',$soft_list); //所有软件
        $this->assign('type_list',$type_list);
        
        $this->assign('url_flag','contract_index'); //left flag
        $this->display();
    }
    
    /**合同删除**/
    public function contract_del(){
        $contract = M('contracts');
        $id = $_REQUEST['id'];
    
        $where_contract['id'] = $id;
        $del = $contract->where($where_contract)->delete();
        if ($del) {
    
            $del_soft_link = M('contract2soft')->where("contractid = $id")->delete();
            $del_item_link = M('contract2item')->where("contractid = $id")->delete();
          
    
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Soft/contract_index")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Soft/contract_index")}"; </script>');die;
        }
    }
    
    
    
}
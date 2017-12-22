<?php
namespace Home\Controller;
use Think\Controller;
class  CompanyController extends HomeController {
    
    public function index(){
        $demo = M('company');
        $name= $_REQUEST['name'];
        if (empty($name)) {
            $count = $demo->count();
            $page = new \Think\Page($count,15);
            $list=$demo->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $where['company_name'] = array('like',"%$name%");
            $count = $demo->where($where)->count();
            $page = new \Think\Page($count,15);
            $list=$demo->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }
        
        $map['name'] = $_GET['name'];
        foreach($map as $key=>$val) {
            $p->parameter .= "$key=".urlencode($val)."&";
        }
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        $show = $page->show();
        
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('url_flag','company');
        $this->assign('name',$name);
        $this->display();
    }
    
    public function company_new(){
        if (IS_POST) {
            $company_name=I('post.company_name');
            $company_desc=I('post.company_desc');
            $description=I('post.description');
            if(empty($company_name)){$this->error('名称不能为空!');}
            if(empty($company_desc)){$this->error('描述不能为空!');}
            if(empty($description)){$this->error('别名不能为空!');}
            $adddata['company_name']=$company_name;
            $adddata['company_desc']=$company_desc;
            $adddata['description']=$description;
            if(M('company')->add($adddata)){
                $this->show('<script type="text/javascript" >alert("添加成功");window.location.href="{:U("index")}"; </script>');
            }else{
                $this->show('<script type="text/javascript" >alert("添加失败");window.location.href="{:U("company_new")}"; </script>');
            }
            exit();
        }
        $this->display();
    }
    
    
    public function company_edit(){
        $demo = M('company');
        $id = $_REQUEST['id'];
        
        if (IS_POST) {
            $company_name=I('post.company_name');
            $company_desc=I('post.company_desc');
            $description=I('post.description');
            if(empty($company_name)){$this->error('名称不能为空!');}
            if(empty($company_desc)){$this->error('描述不能为空!');}
            if(empty($description)){$this->error('别名不能为空!');}
           
            $list=$demo->where('id="'.$id.'" ')->setField(array('company_name'=>$company_name,'company_desc'=>$company_desc,'description'=>$description));
            if($list){
                $this->show('<script type="text/javascript" >alert("编辑成功");window.location.href="{:U("index")}"; </script>');
            }else{
                $this->show('<script type="text/javascript" >alert("编辑失败");window.location.href="{:U("company_edit?id='.$id.'")}"; </script>');
            }
            exit();
        }
        
        $where['id'] = $id;
        $company_info = M('company')->where($where)->find();
        $this->assign('company_info',$company_info);
        $this->display();
    }
    
    public function company_del(){
        $company = M('company');
        $id = $_REQUEST['id'];
        $where_contract['id'] = $id;
        $del = $company->where($where_contract)->delete();
        if ($del) {
            $this->show('<script type="text/javascript" >alert("删除成功");window.location.href="{:U("Company/index")}"; </script>');die;
        }else{
            $this->show('<script type="text/javascript" >alert("删除失败");window.location.href="{:U("Company/index")}"; </script>');die;
        }
    }
    
    
    
    public function company_info(){
        $demo = M('contracts');
        $id = $_REQUEST['id'];
        $where['id'] = $id;
        $company_info = M('company')->where($where)->find();
        
        $name= $_REQUEST['name'];
        if (empty($name)) {
            $wheres['company_id'] = $id;
            $count = $demo->where($wheres)->count();
            $page = new \Think\Page($count,15);
            $list=$demo->where($wheres)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $wheres['company_id'] = $id;
            $wheres['title'] = array('like',"%$name%");
            $count = $demo->where($wheres)->count();
            $page = new \Think\Page($count,15);
            $list=$demo->where($wheres)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        }
        
        
        $map['name'] = $_GET['name'];
        $map['id'] = $_GET['id'];
        foreach($map as $key=>$val) {
            $p->parameter .= "$key=".urlencode($val)."&";
        }
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        //$page->setConfig('theme',"<ul class='pagination'></li><li>%FIRST%</li><li>%UP_PAGE%</li><li>%LINK_PAGE%</li><li>%DOWN_PAGE%</li><li>%END%</li><li><a> %HEADER%  %NOW_PAGE%/%TOTAL_PAGE% 页</a></ul>");
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        $show = $page->show();
        
        $this->assign('id',$id);
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('company_info',$company_info);
        $this->assign('url_flag','company');
        $this->display();
    }
    
    
    public function contract_new() {
        $id = $_REQUEST['id'];
    
        if ( IS_POST ) {

            $Demo = M('contracts');
    
            $title= I('post.title');
            $number= I('post.number');
            $type= I('post.type');
            //$parentid= I('post.parentid');
            $totalcost= I('post.totalcost');
            $startdate= I('post.startdate');
            $currentenddate= I('post.currentenddate');
            $description= I('post.description');
            $comments= I('post.comments');
             
    
            $data['title'] = $title;
            $data['number'] = $number;
            $data['type'] = $type;
            //$data['parentid'] = $parentid;
            $data['company_id'] = $id;
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
                    $this->show('<script type="text/javascript" >alert("合同添加成功");window.location.href="{:U("Company/company_info?id='.$id.'")}"; </script>');
                    exit(); 
                }else{
                    $this->show('<script type="text/javascript" >alert("合同添加失败，请重新添加");window.location.href="{:U("Company/contract_new?id='.$id.'")}"; </script>');
                    
                }
            }else{
                $this->show('<script type="text/javascript" >alert("合同重复添加，请重新添加");window.location.href="{:U("Company/contract_new?id='.$id.'")}"; </script>');
            }
        }
    
        $type_list = M('ipdb_contracttypes')->select();
        $contract_list = M('contracts')->select();
    
        $this->assign('id',$id);
        $this->assign('type_list',$type_list);
        $this->assign('contract_list',$contract_list);
        $this->assign('url_flag','company'); //left flag
        $this->display();
    }
    
    
    public function contract_edit() {
        $id = $_REQUEST['id'];
        $flag = $_REQUEST['flag'];
    
        $Items = M('ipdb_items');
        $Softs = M('software');
        $contract = M('contracts');
        
        
        $contract_info = $contract->where('id="'.$id.'"  ')->find();
    
        if (IS_POST) {
            if ($flag == '1') {
                $title= I('post.title');
                $number= I('post.number');
                $type= I('post.type');
                //$parentid= I('post.parentid');
                $totalcost= I('post.totalcost');
                $startdate= strtotime(I('post.startdate'));
                $currentenddate= strtotime(I('post.currentenddate'));
                $description= I('post.description');
                $comments= I('post.comments');
    
                empty($title) && $this->error('请输入SN号:');
    
                $list=$contract->where('id="'.$id.'"  ')->setField(array('title'=>$title,'number'=>$number,'type'=>$type,'parentid'=>$parentid,'totalcost'=>$totalcost,'startdate'=>$startdate,'currentenddate'=>$currentenddate,'description'=>$description,'comments'=>$comments));
                if ($list==1){
                    $company_id = $contract_info['company_id'];
                    $urls = U("Company/company_info?id=".$company_id);
                    $this->success('基本信息修改成功',$urls);
                    exit();
                }else{
                    $url = U("Company/company_edit?id=".$id);
                    $this->error('基本信息修改失败',$url);
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
                $company_id = $contract_info['company_id'];
                $url = U("Company/company_info?id=".$company_id);
                $this->success('关联硬件修改成功',$url);
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
                $company_id = $contract_info['company_id'];
                $url = U("Company/company_info?id=".$company_id);
                $this->success('关联合同修改成功',$url);
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
                        $company_id = $contract_info['company_id'];
                        $url = U("Company/company_info?id=".$company_id);
                        $this->success('图片修改成功',$url);
                        exit();
                    }
                }
            }
        }
    
        $item_list = $Items->select();
        $soft_list = $Softs->select();
        $contract_list = $contract->where("id != $id")->select();
        
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
    
        $this->assign('url_flag','company'); //left flag
        $this->display();
    }
    
    /**合同删除**/
    public function contract_del(){
        $contract = M('contracts');
        $id = $_REQUEST['id'];
    
        $where_contract['id'] = $id;
        $detail = $contract->where($where_contract)->find();
        $del = $contract->where($where_contract)->delete();
        if ($del) {
    
            $del_soft_link = M('contract2soft')->where("contractid = $id")->delete();
            $del_item_link = M('contract2item')->where("contractid = $id")->delete();
            $company_id = $detail['company_id'];
            $url = U("Company/company_info?id=".$company_id);
            $this->success('删除成功',$url); 
        }else{
            $company_id = $detail['company_id'];
            $url = U("Company/company_info?id=".$company_id);
            $this->error('删除失败',$url); 
        }
    }
    
        public function contract_zichan(){
            
            $Demo = M('zichan');
            
            $contract_id = $_REQUEST['contract_id'];
            $contract_info = M('contracts')->where("id = $contract_id")->find();
            
            $where['contract_id'] = $contract_id;
            $where['type'] = 'port';
            $count = $Demo->where($where)->count();
            $page = new \Think\Page($count,15);
            $list=$Demo->where($where)->order('id desc')->limit($page->firstRow,$page->listRows)->select();
            
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            //$list=$data->select();
            $this->assign('page',$page->show());
            $this->assign('list',$list);
            $this->assign('url_flag','company'); //left flag
            $this->assign('contract_info',$contract_info);
            $this->assign('contract_id',$contract_id);
            $this->display();
        }
    
    
        public function port_new(){
            
            $contract_id = $_REQUEST['contract_id'];
            
            $contract_info = M('contracts')->where("id = $contract_id")->find();
            $company_id = $contract_info['company_id'];
            $companyinfo = M('company')->where("id = $company_id")->find();
            
            if ( IS_POST ) {
                $Demo = M('zichan');
                
                $mis_port= I('post.mis_port');
                //$companyid= I('post.companyid');
                $mis_network= I('post.mis_network');
                $mis_bandwidth= I('post.mis_bandwidth');
                $mis_meta= I('post.mis_meta');
                $type='port';
               
                 
                $data['mis_port'] = $mis_port;
                $data['companyid'] = $company_id;
                $data['companyname'] = $companyinfo['company_name'];
                $data['mis_network'] = $mis_network;
                $data['mis_bandwidth'] = $mis_bandwidth;
                $data['mis_meta'] = $mis_meta;
                $data['type'] = $type;
        
                $checklist=$Demo->where('mis_network = "'.$mis_network.'" and mis_port = "'.$mis_port.'" ')->select();
                if (empty($checklist)){
                    $list=$Demo->data($data)->add();
                    if (!empty($list)){
                        //echo "资产添加成功";
                        $this->show('<script type="text/javascript" >alert("用户添加成功");window.location.href="{:U("Company/contract_zichan")}"; </script>');
                        exit();
                    }
                }else{
                    $this->show('<script type="text/javascript" >alert("用户重复添加，请重新添加");window.location.href="{:U("Company/contract_zichan")}"; </script>');
                }
            }
           
             
            $this->assign('company_name',$companyinfo['company_name']);
            $this->assign('url_flag','company'); //left flag
            $this->display();
        }
        
        
       /* public function contract_zichan(){
        
            $Demo = M('zichan');
        
            $contract_id = $_REQUEST['contract_id'];
            $contract_info = M('contracts')->where("id = $contract_id")->find();
        
            $where['contract_id'] = $contract_id;
            $where['type'] = 'port';
            $count = $Demo->where($where)->count();
            $page = new \Think\Page($count,15);
            $list=$Demo->where($where)->order('id desc')->limit($page->firstRow,$page->listRows)->select();
        
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            //$list=$data->select();
            $this->assign('page',$page->show());
            $this->assign('list',$list);
            $this->assign('url_flag','company'); //left flag
            $this->assign('contract_info',$contract_info);
            $this->assign('contract_id',$contract_id);
            $this->display();
        }*/
    
    
        public function import_companys(){

         
            
            if (IS_POST){
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize   =     10000000 ;// 设置附件上传大小
                $upload->exts      =     array('xlsx','xls');// 设置附件上传类型
                $upload->rootPath  =      './upload/'; // 设置附件上传根目录
                $upload->savePath = 'excels/'; // 设置附件上传（子）目录
                // 上传单个文件
                $info   =   $upload->uploadOne($_FILES['excelfile']);
                if(!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                }else{// 上传成功 获取上传文件信息
                    //echo $info['savepath'].$info['savename'];
                    header('Content-type: text/html; charset=utf-8');
            
                    import("Org.Util.PHPExcel");
                    $PHPExcel=new \PHPExcel();
            
                    if ($info['ext'] == "xls") {
            
                        import("Org.Util.PHPExcel.Reader.Excel5");
            
                        $PHPReader=new \PHPExcel_Reader_Excel5();
            
                        $filename = "./upload/".$info['savepath'].$info['savename'];
                        //载入文件
                        $PHPExcel=$PHPReader->load($filename);
                        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
                        $currentSheet=$PHPExcel->getSheet(0);
                        //获取总列数
                        $allColumn=$currentSheet->getHighestColumn();
                        //获取总行数
                        $allRow=$currentSheet->getHighestRow();
                        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
                        for($currentRow=1;$currentRow<=$allRow;$currentRow++){
                            //从哪列开始，A表示第一列
                            if ($currentRow == 1) {
                                continue;
                            }
            
                            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                                //数据坐标
                                $address = $currentColumn.$currentRow;
                                $temp = $currentSheet->getCell($address)->getValue();
                                 
                                //读取到的数据，保存到数组$arr中
                                $arr[$currentRow][$currentColumn]=$temp;
                                if ($currentColumn == 'U' ) {
                                    break;
                                }
                            }
            
                        }
                        //var_dump($arr);die;
                        /*foreach ($arr as $one){
                            $temp['code'] = $one['A'];
                            $temp['name'] = $one['B'];
                            $temp['device_name'] = $one['C'];
                            $temp['wl_device_name'] = $one['D'];
                            $temp['port_number'] = $one['E'];
                            $temp['port_desc'] = $one['F'];
                            $temp['bandwidth'] = $one['G'];
                            
                            $port->add($temp);
                        }
                        
                        
                        foreach ($arr as $one){
                         $temp['code'] = $one['A'];
                         $temp['name'] = $one['B'];
                         $temp['ip'] = $one['C'];
                         $temp['ip_dec'] = $one['D'];
                    
                         $ip->add($temp);
                         }*/
                        $port = M('pp');
                        $aaa =array();
                        
                        //var_dump($arr);die;
                        
                        foreach ($arr as $one){
                            $temps['name'] = $one['A'];
                            $temps['wl_device_name'] = $one['B'];
                            $temps['port_desc'] = $one['C'];
                            $temps['bandwidth'] = $one['D'];

                            
                            $t = trim($one['B']);
                            $find = M('ipdb_items')->where("common_name = '$t'")->find();
                            if ($find) {
                                $port->add($temps);
                            }else{
                                continue;
                            }
                            
                        }
                        die;
                        //var_dump(count($aaa));
            
                         
                    }else{
                        import("Org.Util.PHPExcel.Reader.Excel2007");
                        $PHPReader=new \PHPExcel_Reader_Excel2007();
            
                        $filename = "./upload/".$info['savepath'].$info['savename'];
                        //载入文件
                        $PHPExcel=$PHPReader->load($filename);
                        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
                        $currentSheet=$PHPExcel->getSheet(0);
                        //获取总列数
                        $allColumn=$currentSheet->getHighestColumn();
                        //获取总行数
                        $allRow=$currentSheet->getHighestRow();
                        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
                        for($currentRow=1;$currentRow<=$allRow;$currentRow++){
                            //从哪列开始，A表示第一列
                            if($currentRow == 1){
                                continue;
                            }
                            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                                //数据坐标
                                $address = $currentColumn.$currentRow;
                                $temp = $currentSheet->getCell($address)->getValue();
                                 
                                //读取到的数据，保存到数组$arr中
                                $arr[$currentRow][$currentColumn]=$temp;
                                if ($currentColumn == 'U' ) {
                                    break;
                                }
            
                            }
            
                        }
                         
                        var_dump($arr);die;
                        //EXCEL_200X_END
                    }
            
                    //INFO_IF_END
                }
                //IS_POST END
            
            
            }
            
            $this->assign('url_flag','company');
            $this->display();
        }
    
    
    
    
}
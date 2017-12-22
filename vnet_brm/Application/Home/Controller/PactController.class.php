<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class  PactController extends HomeController {
    
    public function index(){
        //$Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表;
        //$all = $Model->query("select DISTINCT host as host,hostid from vnet_traffic_portlist GROUP BY hostid");
        $pact_bandwidth = M('erp_pact_ports');
        
        
        
        $condition = array();
        if ($_GET['cust_name']) {
            $condition['cust_name'] = array('like',"%".$_GET['cust_name']."%");
        }
        if ($_GET['pact_code']) {
            $condition['pact_code'] = $_GET['pact_code'];
        }
        if ($_GET['port_code']) {
            $condition['port_code'] = $_GET['port_code'];
        }
        $condition['port_id'] = array('exp','is not null');
        $count = $pact_bandwidth->where($condition)->count();
        $page = new \Think\Page($count,15);
        $pact_list=$pact_bandwidth->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
        
        $pact_arr = array();
        if ($pact_list) {
            foreach ($pact_list as $one){
                $port_id = $one['port_id'];
                if ($port_id) {
                    $port_info = M('traffic_portlist')->where("id = $port_id")->find();
                    $initemid = $port_info['initemid'];
                    $outitemid= $port_info['outitemid'];
                    if (!empty($initemid)) {
                        $in_info = M('zz_port_avg')->where("itemid = $initemid")->find();
                        $out_info = M('zz_port_avg')->where("itemid = $outitemid")->find();
                        $traffic_info = M('traffic_portlist')->where("initemid = $initemid")->find();
                        $pact_arr[$port_id]['oneavg'] = deal_which_bigger($in_info['oneavg'],$out_info['oneavg']);
                        $pact_arr[$port_id]['onemax'] = deal_which_bigger($in_info['onemax'],$out_info['onemax']);
                        $pact_arr[$port_id]['weekmax'] = deal_which_bigger($in_info['weekmax'],$out_info['weekmax']);
                        $pact_arr[$port_id]['ifDescr'] = $traffic_info['ifDescr'];
                    }
                }
                
            }
        }
        
        
        $map['cust_name'] = $_GET['cust_name'];
        $map['pact_code'] = $_GET['pact_code'];
        $map['port_code'] = $_GET['port_code'];
        foreach($map as $key=>$val) {
            $p->parameter .= "$key=".urlencode($val)."&";
        }
        
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        $show = $page->show();
        
        $this->assign("pact_arr",$pact_arr);
        $this->assign('page',$show);
        $this->assign('pact_list',$pact_list);
        $this->assign('url_flag','pact_index'); //left flag
        $this->display();
    }
    
    
    public function unindex(){
        //$Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表;
        //$all = $Model->query("select DISTINCT host as host,hostid from vnet_traffic_portlist GROUP BY hostid");
        $pact_bandwidth = M('erp_pact_ports');
    
    
    
        $condition = array();
        if ($_GET['cust_name']) {
            $condition['cust_name'] = array('like',"%".$_GET['cust_name']."%");
        }
        if ($_GET['pact_code']) {
            $condition['pact_code'] = $_GET['pact_code'];
        }
        if ($_GET['port_code']) {
            $condition['port_code'] = $_GET['port_code'];
        }
        $condition['port_id'] = array('exp','is null');
        $count = $pact_bandwidth->where($condition)->count();
        $page = new \Think\Page($count,15);
        $pact_list=$pact_bandwidth->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
    
        
    
    
        $map['cust_name'] = $_GET['cust_name'];
        $map['pact_code'] = $_GET['pact_code'];
        $map['port_code'] = $_GET['port_code'];
        foreach($map as $key=>$val) {
            $p->parameter .= "$key=".urlencode($val)."&";
        }
    
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        $show = $page->show();

        $this->assign('page',$show);
        $this->assign('pact_list',$pact_list);
        $this->assign('url_flag','pact_index'); //left flag
        $this->display();
    }
    
    public function haveindex(){
        //$Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表;
        //$all = $Model->query("select DISTINCT host as host,hostid from vnet_traffic_portlist GROUP BY hostid");
        $pact_bandwidth = M('erp_pact_ports');
        $item = M('ipdb_items');
    
        $condition = array();
        if ($_GET['cust_name']) {
            $condition['cust_name'] = array('like',"%".$_GET['cust_name']."%");
        }
        if ($_GET['pact_code']) {
            $condition['pact_code'] = $_GET['pact_code'];
        }
        if ($_GET['port_code']) {
            $condition['port_code'] = $_GET['port_code'];
        }
        $item_names = $item->where("itemtypeid = 1 and depart_id in (4,5,6,8)")->field('common_name')->select();
        $item_names_arr = array();
        foreach ($item_names as $one) {
            $item_names_arr[]=$one['common_name'];
        }
        
        
        $condition['wl_device_name'] = array('in',$item_names_arr);
        $condition['port_id'] = array('exp','is null');
        $count = $pact_bandwidth->where($condition)->count();
        $page = new \Think\Page($count,15);
        $pact_list=$pact_bandwidth->where($condition)->limit($page->firstRow.','.$page->listRows)->group('wl_device_name')->select();
    
        $pact_arr = array();
        if ($pact_list) {
            foreach ($pact_list as $one){
                $port_id = $one['port_id'];
                if ($port_id) {
                    $port_info = M('traffic_portlist')->where("id = $port_id")->find();
                    $initemid = $port_info['initemid'];
                    $outitemid= $port_info['outitemid'];
                    if (!empty($initemid)) {
                        $in_info = M('zz_port_avg')->where("itemid = $initemid")->find();
                        $out_info = M('zz_port_avg')->where("itemid = $outitemid")->find();
                        $pact_arr[$port_id]['oneavg'] = deal_which_bigger($in_info['oneavg'],$out_info['oneavg']);
                        $pact_arr[$port_id]['onemax'] = deal_which_bigger($in_info['onemax'],$out_info['onemax']);
                        $pact_arr[$port_id]['weekmax'] = deal_which_bigger($in_info['weekmax'],$out_info['weekmax']);
                    }
                }
    
            }
        }
    
    
        $map['cust_name'] = $_GET['cust_name'];
        $map['pact_code'] = $_GET['pact_code'];
        $map['port_code'] = $_GET['port_code'];
        foreach($map as $key=>$val) {
            $p->parameter .= "$key=".urlencode($val)."&";
        }
    
        $page->setConfig('header','共');
        $page->setConfig('first','«');
        $page->setConfig('last','»');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %NOW_PAGE%/%TOTAL_PAGE% 页');
        $show = $page->show();
    
        $this->assign("pact_arr",$pact_arr);
        $this->assign('page',$show);
        $this->assign('pact_list',$pact_list);
        $this->assign('url_flag','pact_index'); //left flag
        $this->display();
    }
    

}
<?php

//处理结果返回 数组

function deal_ids($rzt){
    $temp = array();
    foreach ($rzt as $val) {
        $temp[] = $val['id'];
    }
    return $temp;
}

function deal_array($rzt,$id,$can){
    $temp = array();
    foreach ($rzt as $val) {
        $temp[$val["$id"]] = $val[$can];
    }
    return $temp;
}

function history($module_name,$user,$module_id,$desc){
    $his = M('history');
    $data['module'] = $module_name;
    $data['user'] = $user;
    $data['module_id'] = $module_id;
    $data['desc'] = $desc;
    $data['date'] = time();
    $list=$his->data($data)->add();
}

function count_u_number($str){
    if($str=='noarea'){
        return 0;
    }else{
        $arr_temp = explode(',', $str);
        return count($arr_temp);
    }
    
}

function deal_array_val_to_key($arr,$val_field,$key_field){
    $temp = array();
    foreach ($arr as $val) {
        $temp[$val["$val_field"]] = $val["$key_field"];
    }
    return $temp;

}

function is_login(){
   
        $user = session('user_auth');
        if (empty($user)) {
            return 0;
        } else {
            return $user['uid'];
        }
    
}


/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name,$vars=array()){
    $array     = explode('/',$name);
    $method    = array_pop($array);
    $classname = array_pop($array);
    $module    = $array? array_pop($array) : 'Common';
    $callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
    if(is_string($vars)) {
        parse_str($vars,$vars);
    }
    return call_user_func_array($callback,$vars);
}


/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author 杨坤
 */
function is_administrator($uid = null){
    $uid = is_null($uid) ? is_login() : $uid;
    $uid = intval($uid);
    return $uid && (in_array($uid, C('USER_ADMINISTRATOR')));
}


/**无限级分类**/
function getTree($arr, $pid=0,$deep=0)
{
    static $tree = array();
    foreach($arr as $k=>$row)
    {
        if( $row['pid'] == $pid )
        {
            $row['deep'] = $deep;
            $tree[] = $row;
            unset($arr[$k]);
            getTree($arr,$row['id'],$deep+1);
        }
    }
    return $tree;
}

/**获取当前用户的组  关联的hostgroup**/
function get_hostgroup_by_uid($uid){
    $usergroup_info = M('user')->where("id = $uid")->find();
    $usergroup_id = $usergroup_info['usergroup_id'];
    $depart_arr = M('user_depart_config')->where("usergroup_id = $usergroup_id")->field('depart_id')->select();
    $temp = array();
    if (count($depart_arr)>0) {
        foreach($depart_arr as $depart) {
            $temp[]=intval($depart['depart_id']);
        }
    }
    return $temp;
}

function get_username_by_uid($uid){
    $usergroup_info = M('user')->where("id = $uid")->find();
    return $usergroup_info['username'];
}




function single_depart_id($uid) {
    $usergroup_info = M('user')->where("id = $uid")->find();
    $usergroup_id = $usergroup_info['usergroup_id'];
    $list = M('department')->where("group_id = $usergroup_id")->find();
    if ($list) {
        return $list['id'];
    }else{
        return 0;
    }
}

function get_db(){
    $zabbixs = M("history_uint","",DB_CONFIG1);
    var_dump($zabbixs);
}

function deal_itemid_lastvalue($all){
    $temp = array();
    if (empty($all)) {
        return $temp;
    }
    foreach ($all as $one){
        $item_id = $one->itemid;
        $lastvalue = $one->lastvalue;
        $temp[$item_id] = $lastvalue;
    }
    return $temp;
}


function outputCurl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function deal_portid_portname($all){
    $temp = array();
    foreach ($all as $one){
        $temp[$one['id']] = $one['portname'];
    }
    return $temp;
}

function deal_traffic_alert($band,$in,$out) {
    
            $in = intval($in);
            $out = intval($out);
            $temp_band = 0;
            if (strpos($band, "无限速") || strpos($band, "不限速")) {
                $temp_band = 1000000000000;
            }
            
            if (empty($band) || $band == "") {
                $temp_band = 0;
            }
            
            if (strpos($band, "G")) {
                if(preg_match("/(\d+)\.(\d+)/is", $band, $match)){
                    $temp_band = (1024*1024*1024*$match[0]);
                }elseif(preg_match("/(\d+)/", $band, $matchs)){
                    $temp_band = (1024*1024*1024*$matchs[0]);
                }else{
                    $temp_band = 0;
                }
            }elseif(strpos($band, "M")) {
                if(preg_match("/(\d+)\.(\d+)/is", $band, $match)){
                    $temp_band = (1024*1024*$match[0]);
                }elseif(preg_match("/(\d+)/", $band, $matchs)){
                    $temp_band = (1024*1024*$matchs[0]);
                }else{
                    $temp_band = 0;
                }
            }else{
                if(preg_match("/(\d+)\.(\d+)/is", $band, $match)){
                    $temp_band = (1024*1024*$match[0]);
                }elseif(preg_match("/(\d+)/", $band, $matchs)){
                    $temp_band = (1024*1024*$matchs[0]);
                }else{
                    $temp_band = 0;
                }
            }
            
            $state = 0;
            if ($in > $temp_band) {
                $state = 1;
                return $state;
            }
            
            if ($out > $temp_band) {
                $state = 1;
                return $state;
            }
            
            return $state;
            
}

function deal_status_by_itemid($itemid){
    import("Org.Util.ZabbixApiAbstract");
    import("Org.Util.ZabbixApi");
    $api = new \ZabbixApi("http://211.151.5.46/zabbix/api_jsonrpc.php", "Admin", "buzhidao");
    $item_get = $api->itemGet($params=array("output"=>"extend","itemids"=>$itemid));
    $lastvalue = $item_get[0]->lastvalue;
    return $lastvalue;
}

function deal_if_desc($ifdesc){
    if (strpos($ifdesc, "Interface") || $ifdesc=="") {
        return "";
    }else{
        return $ifdesc;
    }
}


function deal_fazhi($band) {

    $temp_band = "0";
    if (strpos($band, "无限速") || strpos($band, "不限速")) {
        $temp_band = "无";
        return $temp_band;
    }

    if (empty($band) || $band == "") {
        $temp_band = "0";
        return $temp_band;
    }

    if (strpos($band, "G")) {
        if(preg_match("/(\d+)\.(\d+)/is", $band, $match)){
            $temp_band = (1024*$match[0])."";
        }elseif(preg_match("/(\d+)/", $band, $matchs)){
            $temp_band = (1024*$matchs[0])."";
        }else{
            $temp_band = "0";
        }
        return $temp_band;
    }elseif(strpos($band, "M")) {
        if(preg_match("/(\d+)\.(\d+)/is", $band, $match)){
            $temp_band = $match[0]."";
        }elseif(preg_match("/(\d+)/", $band, $matchs)){
            $temp_band = $matchs[0]."";
        }else{
            $temp_band = 0;
        }
        return $temp_band;
    }else{
        if(preg_match("/(\d+)\.(\d+)/is", $band, $match)){
            $temp_band = $match[0];
        }elseif(preg_match("/(\d+)/", $band, $matchs)){
            $temp_band = $matchs[0];
        }else{
            $temp_band = 0;
        }
        return $temp_band;
    }

   
    return $temp_band;

}


function deal_which_bigger($a,$b) {
    if ($a>=$b) {
        return $a;
    }else{
        return $b;
    }
}

/** 秒数  转换成年月日   **/
function Sec2Time($time){
    if(is_numeric($time)){
        $value = array(
            "years" => 0, "days" => 0, "hours" => 0,
            "minutes" => 0, "seconds" => 0,
        );
        if($time >= 31556926){
            $value["years"] = floor($time/31556926);
            $time = ($time%31556926);
        }
        if($time >= 86400){
            $value["days"] = floor($time/86400);
            $time = ($time%86400);
        }
        if($time >= 3600){
            $value["hours"] = floor($time/3600);
            $time = ($time%3600);
        }
        if($time >= 60){
            $value["minutes"] = floor($time/60);
            $time = ($time%60);
        }
        $value["seconds"] = floor($time);
        //return (array) $value;
        $t = "";
        if ($value["years"]!=0) {
            $t.= $value["years"] ."年";
        }
        
        if ($value["days"]!=0) {
            $t.= $value["years"] ."天";
        }
        $t.=" ". $value["hours"] ."小时". $value["minutes"] ."分".$value["seconds"]."秒";
        Return $t;

    }else{
        return (bool) FALSE;
    }
}





<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;
use Home\Model\AuthRuleModel;
/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {

	
    

    protected function _initialize(){
        /* 读取站点配置 */
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
        
        $uid = is_login();
        if(!$uid){
            $this->error('请重新登录',U('Login/index'));
        }
        
        //单独depart_id
        $depart_id = single_depart_id($uid);
        
        // 是否是超级管理员
        define('IS_ROOT',   is_administrator());
        // 是否是组管理员
        define('IS_OWNER',  $_SESSION['user_auth']['is_owner']);
        
        $this->assign('single_depart_id',$depart_id); //是否是超级管理员
        $this->assign('is_admin_define',is_administrator()); //是否是超级管理员
        $this->assign('is_owner_define',$_SESSION['user_auth']['is_owner']); //是否是部门管理员
        
        
        // 检测系统权限
        if(!IS_ROOT){
            $rule  = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
            if ($rule!='home/login/login' && $rule!='home/login/logout' && $rule!='home/index/index' && $rule!='home/user/usergroup_access'){ // by zwj 如果是初始化登陆页面和推出页面不进行授权判断
            
                if ( !$this->checkRule($rule,array('in','1,2')) ){ //const RULE_URL = 1;const RULE_MAIN = 2;
                    is_login() || $this->error('您登录超时，请先登录！', U('User/login'));
                    $this->error('未授权访问!!'); //BY ZWJ
                }
            
            }
            //--------------------
        }
        
        
        
    }
    
    
    final protected function checkRule($rule, $type=AuthRuleModel::RULE_URL, $mode='url'){//$type=AuthRuleModel::RULE_URL
        static $Authindex    =   null;
        if (!$Authindex) {
            $Authindex       =   new \Think\Authindex();
        }
        $uid = is_login();
        if(!$Authindex->check($rule,$uid,$type,$mode)){ // home/index/portal,22,1,url
            //echo $rule."-------------";
            return false;
        }
        return true;
    }

	
    final protected function accessControl(){
        $allow = C('ALLOW_VISIT');
        $deny  = C('DENY_VISIT');
        $check = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);//by zwj ???CONTROLLER_NAME.'/'.ACTION_NAME
        if ( !empty($deny)  && in_array_case($check,$deny) ) { //in_array_case from thinkphp function
            return false;//非超管禁止访问deny中的方法
        }
        if ( !empty($allow) && in_array_case($check,$allow) ) {
            return true;
        }
        return null;//需要检测节点权限
    }

    function array_column($oInput, $sColumnKey = 'id', $sIndexKey = null) {
        if (empty($oInput) || !is_array($oInput)) return array();
        $bColumnKeyIsNumber = (is_numeric($sColumnKey)) ? true : false;
        $bIndexKeyIsNull = (is_null($sIndexKey)) ? true : false;
        $bIndexKeyIsNumber = (is_numeric($sIndexKey)) ? true : false;
        $aOutput = array();
        foreach ((array)$oInput as $_sKey => $_Val) {
            if ($bColumnKeyIsNumber) {
                $_Tmp = array_slice($_Val, $sColumnKey, 1); // 从数组中取出一段
                $_Tmp = (is_array($_Tmp) && !empty($_Tmp)) ? current($_Tmp) : null; // 返回数组中的当前单元
            } else {
                $_Tmp = $sColumnKey != null ? isset($_Val[$sColumnKey]) ? $_Val[$sColumnKey] : null : $_Val;
            }
            if (!$bIndexKeyIsNull) {
                if ($bIndexKeyIsNumber) {
                    $_sKey = array_slice($_Val, $sIndexKey, 1);
                    $_sKey = (is_array($_sKey) && !empty($_sKey)) ? current($_sKey) : null;
                    $_sKey = is_null($_sKey) ? 0 : $_sKey;
                } else {
                    $_sKey = isset($_Val[$sIndexKey]) ? $_Val[$sIndexKey] : 0;
                }
            }
            $aOutput[$_sKey] = $_Tmp;
        }
        unset($oInput, $_Val);
        return $aOutput;
    }
    
    
    
    
    
    
    
    /**
     * 获取Tree内容 根据 contypeid
     * @param int $iParentID
     * @param array $aDataList
     * @return array
     */
    function getTreeList($iParentID, $aDataList) {
        $aTreeList = array();
        foreach ($aDataList as $aData) {
            if ($aData['contypeid'] == $iParentID) {
                $_aTree = $this->getTreeList($aData['id'], $aDataList);
                if (!empty($_aTree)) $aData['list'] = $_aTree;
                $aTreeList[$aData['id']] = $aData;
            }
        }
        return $aTreeList;
    }
    
    /**
     * 获取父级信息
     * @param int $iID
     * @param array $aDataList
     * @param array $aParents
     * @return array
     */
    function getParentsByID($iID, $aDataList, $aParents = array()) {
        $aData = isset($aDataList[$iID]) ? $aDataList[$iID] : null;
        if (empty($aParents)) $aParents = $aData;
        if (isset($aDataList[$aData['contypeid']])) {
            $_aData = $aDataList[$aData['contypeid']];
            $_aData['list'] = $aParents;
            return getParentsByID($_aData['id'], $aDataList, $_aData);
        } else {
            return $aParents;
        }
    }
    
    /**
     * 生成 option
     * @param array $aDataList
     * @param null $sSelected 选中项
     * @param int $iLevel 层级
     * @return string
     */
    function createOptions($aDataList, $sSelected = null, $iLevel = 0) {
        $sOptions = null;
        if (empty($aDataList) || !is_array($aDataList)) return $sOptions;
        $sPrefix = (!empty($iLevel)) ? str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $iLevel) : null;
        foreach ($aDataList AS $aData) {
            $sOptions .= '<option value="' . $aData['id'] . '"';
            $sOptions .= (!empty($sSelected) & $aData['id'] == $sSelected) ? ' selected ' : null;
            $sOptions .= '>' . $sPrefix . $aData['name'] . '</option>';
            if (isset($aData['list']) && !empty($aData['list'])) {
                $sOptions .= $this->createOptions($aData['list'], $sSelected, ($iLevel + 1));
            }
        }
        return $sOptions;
    }









	

}

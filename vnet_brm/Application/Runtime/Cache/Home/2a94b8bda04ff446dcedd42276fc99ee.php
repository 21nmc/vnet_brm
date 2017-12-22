<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
  <head>
	
<meta charset="utf-8">
    <title>机柜资产管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="/vnet_brm/Public/lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/vnet_brm/Public/css/main.css">
    <link rel="stylesheet" type="text/css" href="/vnet_brm/Public/static/ly/ly.css">
    <link rel="stylesheet" type="text/css" href="/vnet_brm/Public/static/ly/tipso.css">
    <link rel="Shortcut Icon" href="/vnet_brm/Public/static/ly/favicon.ico">
    <script src="/vnet_brm/Public/lib/jquery-1.7.2.min.js" type="text/javascript"></script>
    <!--[if lt IE 9]>
      <script src="/vnet_brm/Public/lib/html5.js"></script>
    <![endif]-->
    
    <style type="text/css">

span.current{ text-align:center; font-size:12px; cursor:default;line-height:28px; height:28px; width:28px; border:1px solid rgba(0,0,0,0);  display:inline-block; margin:0 2px;}
a.num,a.next,a.prev{ line-height:28px; height:28px; width:28px; text-align:center; font-size:12px; border: 1px solid rgba(0,0,0,.2); color: rgba(0,0,0,.8); display:inline-block; margin:0 2px;}
a.num:hover,a.next:hover,a.prev:hover{ background:rgba(0,0,0,.1); color:rgba(0,0,0,.8);}
button{ font-size:14px; font-family:"Microsoft YaHei"; cursor:pointer; padding:10px 20px; background:#5089E1; color:#fff; border-radius:4px; -o-border-radius:4px; -os-border-radius:4px; -moz-border-radius:4px; -webkit-border-radius:4px; transition:all 0.3s ease; border:none; display:block; margin:20px auto;}
button:hover{ background:#3d76cf; }

		tr.red{ background:red; }
		tr.green{ background:green; }
		tr.yellow{ background:yellow; }
    </style>



 </head>
<body>
        <!-- 头部 -->
        <!--the top nav begin-->    
    <div class="navbar navbar-default" role="navigation">
        <div class="navbar-inner">
        	<div class="navbar-header">
                <a class="navbar-brand" href="">机柜资产管理系统</a>
             </div>
                <ul class="nav navbar-nav pull-right" >
                
                	<li id= "fat-menu" class="dropdown">
                        <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-cloud"></span> 企业系统地图
                            <span class="glyphicon glyphicon-chevron-down"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="" target="_blank">服务管理</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="http://211.151.5.12/boss/index.php/Home/Index/index.html" target="_blank">客户服务</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="">工单管理</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="http://211.151.2.75/vnet/index.php?s=/Home/Index/index.html" target="_blank">测试服务</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="http://211.151.5.29/vianet/index.php?s=/Home/Index/index.html">网络管理</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="">系统管理</a></li>
                        </ul>
                    </li>
                
                    <li id= "fat-menu" class="dropdown">
                        <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-user"></span> <?php echo ($_SESSION['user_auth']['username']); ?>
                            <span class="glyphicon glyphicon-chevron-down"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo U('Login/logout');?>">登出</a></li>
                        </ul>
                    </li>
                </ul>
        </div>
    </div>

	    <!-- /头部 -->
		
<!--the sidebar nav begin -->
<span class="leftsider">折叠</span>
    <div class="sidebar-nav">

    	<a href="<?php echo U('Index/index');?>" class="nav-header" ><span class="glyphicon glyphicon-home"></span> 管理中心</a>
    	
        
        
        <a href="#account-menu2" class="nav-header collapsed" data-toggle="collapse"><span class="glyphicon glyphicon-hd-video"></span> 资产管理 <span class="glyphicon glyphicon-chevron-up"></span></a>
        <ul id="account-menu2" class="nav nav-list collapse">
            <li <?php if($url_flag == 'item_index' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Item/index');?>">硬件列表</a></li>
            <?php if($is_admin_define == true): ?><li <?php if($url_flag == 'soft_index' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Soft/index');?>">软件列表</a></li><?php endif; ?>
            <li <?php if($url_flag == 'device_list' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Customer/device_list');?>">各地区网络设备列表</a></li>
            <!-- <li <?php if($url_flag == 'contract_index' ): ?>class="active"<?php endif; ?> ><a  href="<?php echo U('Soft/contract_index');?>">合同列表</a></li> -->
            <?php if($is_admin_define == true): ?><li <?php if($url_flag == 'lei_index' ): ?>class="active"<?php endif; ?> ><a  href="<?php echo U('Ying/itemtypes');?>">类型管理列表</a></li><?php endif; ?>
            
        </ul>
        
         <!--a href="#account-menu9" class="nav-header collapsed" data-toggle="collapse"><span class="glyphicon glyphicon-hd-video"></span> 客户资产管理 <span class="glyphicon glyphicon-chevron-up"></span></a>
        <ul id="account-menu9" class="nav nav-list collapse">
            <li <?php if($url_flag == 'customer_index' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Customer/index');?>">客户列表</a></li>
            <li <?php if($url_flag == 'pact_index' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Pact/index');?>">合同带宽列表</a></li>
            <?php if(($single_depart_id == 5) or ($is_admin_define == true) ): ?><li <?php if($url_flag == 'customer_alert' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Customer/alert');?>">客户超带宽告警</a></li><?php endif; ?>
            <?php if($single_depart_id == 4 ): ?><li <?php if($url_flag == 'customer_alert' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Customer/alert_huadong');?>">客户超带宽告警</a></li><?php endif; ?>
            <?php if($single_depart_id == 6 ): ?><li <?php if($url_flag == 'customer_alert' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Customer/alert_huanan');?>">客户超带宽告警</a></li><?php endif; ?>
            <?php if($single_depart_id == 8 ): ?><li <?php if($url_flag == 'customer_alert' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Customer/alert_chuanshu');?>">客户超带宽告警</a></li><?php endif; ?>
            <li <?php if($url_flag == 'customer_contract' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Customer/customer_contract');?>">客户合同列表</a></li>
        </ul-->
        
        <a href="#account-menu3" class="nav-header collapsed" data-toggle="collapse"><span class="glyphicon glyphicon-hd-video"></span> 机柜管理 <span class="glyphicon glyphicon-chevron-up"></span></a>
        <ul id="account-menu3" class="nav nav-list collapse">
            <li <?php if($url_flag == 'listlocations' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Ipdb/listlocations');?>">数据中心</a></li>
            <li <?php if($url_flag == 'listareas' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Ipdb/listareas');?>">房间列表</a></li>
            <li <?php if($url_flag == 'listracks' ): ?>class="active"<?php endif; ?> ><a  href="<?php echo U('Racks/listracks');?>">机柜列表</a></li>
            <li <?php if($url_flag == 'rackmap' ): ?>class="active"<?php endif; ?> ><a  href="<?php echo U('Racks/rackmap');?>">机柜综合查询</a></li>
            <li <?php if($url_flag == 'reportmap' ): ?>class="active"<?php endif; ?> ><a  href="<?php echo U('Racksreport/reportmap');?>">综合报表</a></li>
            <?php if($is_admin_define == true): ?><!--li <?php if($url_flag == 'listnodes' ): ?>class="active"<?php endif; ?> ><a  href="<?php echo U('Node/listnodes');?>">节点列表</a></li>
            <li <?php if($url_flag == 'racks_index' ): ?>class="active"<?php endif; ?> ><a  href="<?php echo U('Racks/index');?>">机柜九宫格</a></li--><?php endif; ?>
            
        </ul>

        <!--a href="#account-menu6" class="nav-header collapsed" data-toggle="collapse"><span class="glyphicon glyphicon-hd-video"></span> 资源管理 <span class="glyphicon glyphicon-chevron-up"></span></a>
        <ul id="account-menu6" class="nav nav-list collapse">
            <li <?php if($url_flag == 'events' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Resource/events');?>">事件管理</a></li>
            <li <?php if($url_flag == 'ip' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Ip/index');?>">IP资源管理</a></li>
            <li <?php if($url_flag == 'cactimap' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Resource/cactimap');?>">出口菜单</a></li>
            <li <?php if($url_flag == 'cactiindex' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Resource/cactiindex');?>">华北、华东、华南数据列表</a></li>
            <li <?php if($url_flag == 'Engineroomport' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Engineroomport/index');?>">华北机房端口管理</a></li>
            <li <?php if($url_flag == 'huanan' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Engineroomport/indexhuanan');?>">华南机房端口管理</a></li>
            <li <?php if($url_flag == 'huadong' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Engineroomport/indexhuadong');?>">华东机房端口管理</a></li>
            <li <?php if($url_flag == 'chukou' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('ExportFlow/index');?>">出口端口管理</a></li>
            <li <?php if($url_flag == 'weekchukou' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Weekreport/index');?>">周报端口管理</a></li>
            <li <?php if($url_flag == 'Mis' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Mis/index');?>">MIS周报管理</a></li>

        </ul-->

        
        <?php if($is_admin_define == true): ?><a href="#account-menu4" class="nav-header collapsed" data-toggle="collapse"><span class="glyphicon glyphicon-hd-video"></span> 用户及用户组权限管理 <span class="glyphicon glyphicon-chevron-up"></span></a>
        <ul id="account-menu4" class="nav nav-list collapse">
            <li <?php if($url_flag == 'user_list' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('User/user_list');?>">用户列表</a></li>
            <li <?php if($url_flag == 'user_group_list' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('User/user_group_list');?>">用户组列表</a></li>
            <li <?php if($url_flag == 'host_group_list' ): ?>class="active"<?php endif; ?> ><a  href="<?php echo U('HostGroup/host_group_list');?>">系统列表</a></li>
            <li <?php if($url_flag == 'relation_list' ): ?>class="active"<?php endif; ?> ><a  href="<?php echo U('HostGroup/relation_list');?>">关系授权</a></li>
            <li <?php if($url_flag == 'menu' ): ?>class="active"<?php endif; ?> ><a  href="<?php echo U('Menu/menulist');?>">菜单管理</a></li>
            <li <?php if($url_flag == 'squared' ): ?>class="active"<?php endif; ?> ><a  href="<?php echo U('Squared/squared_manger');?>">九宫格管理</a></li>
            <!--li <?php if($url_flag == 'appliaction' ): ?>class="active"<?php endif; ?> ><a  href="<?php echo U('Appliaction/manger');?>">全局用户授权</a></li-->

        </ul>
        <?php elseif($is_owner_define == 'YES'): ?>
        <a href="#account-menu4" class="nav-header collapsed" data-toggle="collapse"><span class="glyphicon glyphicon-hd-video"></span> 部门用户管理 <span class="glyphicon glyphicon-chevron-up"></span></a>
        <ul id="account-menu4" class="nav nav-list collapse">
            <li <?php if($url_flag == 'owner_user_list' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('User/owner_user_list');?>">用户列表</a></li>
        </ul>
        <?php else: endif; ?>

        <a href="#account-menu5" class="nav-header collapsed" data-toggle="collapse"><span class="glyphicon glyphicon-user"></span> 账户管理 <span class="glyphicon glyphicon-chevron-up"></span></a>
        <ul id="account-menu5" class="nav nav-list collapse">
            <li <?php if($url_flag == 'change_pwd' ): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Login/profile');?>">修改密码</a></li>
        </ul>

    </div>
    <!--the siderbar nav en-->

<script type="text/javascript">
$(function(){
    $(".leftsider").toggle(function(){
        $(this).css('left',0).html('展开');
        $('.sidebar-nav').hide();
        $('.content').css('margin-left',0)
    },function(){
        $(this).css('left',240).html('折叠');
        $('.sidebar-nav').show();
        $('.content').css('margin-left',240)
    });
})
</script>

		<!-- 主体 -->
		


<div class="content">
        <div class="header">
            	<!--the home stats begin-->
        
    	<!--the home stats end and page title begin-->
        <h1 class="page-title">管理中心</h1>
        </div>
         <ul class="breadcrumb">
           <!--  
        	<li><a href="index.html">首页</a> <span class="divider">/</span></li>
            <li class="active">管理中心</li>-->
        </ul> 
        <!--page title end and the main content begin-->
        
        
        
        <div class="container">
        	<!-- the alert info begin -->
        	
        	
        	<!-- the alert info end and the panel begin-->
        	<div class="panel panel-default" id="all-device-info">
        		<div class="panel-heading">数据中心 - <?php echo ($usergroup_name); ?></div>
        		  <div class="panel-body">
        			 <div class="stat-widget-container">
        				    <div class="col-xs-4">
        					   <a href="<?php echo U('Item/index');?>"><p class="number"><?php echo ($items_count); ?> <span>台</span></p></a>
        					   <p class="detail">所属设备数</p>
        				    </div>
            			     <div class="col-xs-4">
        					   <p class="number"><?php echo ($racks_count); ?> <span>台</span></p>
        					   <p class="detail">所属机柜总数</p>
        				    </div>  
        				    <div class="col-xs-4">
        					   <p class="number">5 <span>个</span></p>
        					   <p class="detail">所属资源</p>
        				    </div>    				
        			 </div>
        		  </div>
        	</div>
            
            <div class="row">
            
                <div class="col-xs-4">
                    <div class="panel panel-default" id="ledpanel">
                        <div class="panel-heading">
                            <h5>设备列表<span class="make-switch pull-right" data-on="success" data-off="danger"></span></h5>
                        </div>
                         <table class="table">
                        <thead><tr>
                            <th>名称</th>
                            <th>IP</th>
                            <th>状态</th>
                            <th>所占U数</th>
                            
                        </tr></thead>
                        <tbody>
                            <?php if(is_array($item_list)): $i = 0; $__LIST__ = $item_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                <td><?php echo ($vo["common_name"]); ?></td>
                                <td><?php echo ($vo["ipv4"]); ?></td>
                                <td>正常</td>
                                <td><?php echo ($vo["usize"]); ?></td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                        </table>
                    <div class="more-device-button">
                            <a href="<?php echo U('Item/index');?>"><button  class="btn btn-primary"> 查看资产列表 </button></a>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                    <div class="panel panel-default" id="ledpanel">
                        <div class="panel-heading">
                            <h5>机柜列表<span class="make-switch pull-right" data-on="success" data-off="danger"></span></h5>
                        </div>
                         <table class="table">
                        <thead><tr>
                            <th>名称</th>
                            <th>租用方式</th>
                            <th>U位</th>
                        </tr></thead>
                        <tbody>
                          <?php if(is_array($rack_list)): $i = 0; $__LIST__ = $rack_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                <td><?php echo ($vo["name"]); ?></td>
                                <td><?php echo ($vo["name"]); ?></td>
                                <td><?php echo ($vo["name"]); ?></td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>  
                        </tbody>
                        </table>
                    <div class="more-device-button">
                            <a href="<?php echo U('Racks/listracks');?>"><button  class="btn btn-primary"> 查看机柜列表 </button></a>
                    </div>
                </div>
            </div>
            
            <div class="col-xs-4">
                    <div class="panel panel-default" id="ledpanel">
                        <div class="panel-heading">
                            <h5>资源列表<span class="make-switch pull-right" data-on="success" data-off="danger"></span></h5>
                        </div>
                         <table class="table">
                        <thead><tr>
                            <th>名称</th>
                            <th>状态</thz>
                            <th>时间</th>
                            <th>操作</th>
                        </tr></thead>
                        <tbody>
                            <tr>
                                <td>审批01</td>
                                <td><span class="label label-success">已处理</span></td>
                                <td>2017-1-23</td>
                                <td><a href="#" class="btn btn-danger btn-xs">不再显示</a></td>
                            </tr>
                            <tr>
                                <td>审批02</td>
                                <td><span class="label label-success">已处理</span></td>
                                <td>2017-1-24</td>
                                <td><a href="#" class="btn btn-danger btn-xs">不再显示</a></td>
                            </tr>
                        </tbody>
                        </table>
                    <div class="more-device-button">
                            <button  class="btn btn-primary" data-toggle="modal" data-target="#led-more-device"> 查看资源列表 </button>
                    </div>
                </div>
            </div>
            
            </div>
            <!--  <div class="row">
                <div class="col-xs-6" id="device-info">
                <div class="panel panel-default">
                    <div class="panel-heading"><span class="glyphicon glyphicon-transfer"></span> 待办工作</div>
                    <table class="table">
                        <thead><tr>
                            <th>代办工作名称</th>
                            <th>状态</th>
                            <th>工程师</th>
                        </tr></thead>
                        <tbody>
                            <tr>
                                <td>代办工作1</td>
                                <td><span class="label label-success">已完成</span></td>
                                <td>某某</td>
                            </tr>
                            <tr>
                                <td>代办工作2</td>
                                <td><span class="label label-danger">未完成</span></td>
                                <td>某某</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>

                <div class="col-xs-6" id="sensor-device-info">
                <div class="panel panel-default">
                    <div class="panel-heading"><span class="glyphicon glyphicon-hd-video"></span> 故障统计</div>
                    <table class="table">
                        <thead><tr>
                            <th>故障名称</th>
                            <th>状态</th>
                            <th>时间</th>
                        </tr></thead>
                        <tbody>
                            <tr>
                                <td>故障1</td>
                                <td><span class="label label-danger">严重</span></td>
                                <td>2016-11-02 11:22:22</td>
                            </tr>
                            <tr>
                                <td>故障2</td>
                                <td><span class="label label-success">一般</span></td>
                                <td>2016-11-03 12:22:22</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
          -->
        <!--the content end -->
      
                    <footer>
                        <hr>
                        <p class="pull-right"></p>
                    </footer>
                    
    </div>



		<!-- /主体 -->

   <!-- 底部 -->
		

<style>
.none-coco{ display:none;}
</style>

<script src="/vnet_brm/Public/lib/bootstrap/js/bootstrap.js"></script>
<script>

$('.sidebar-nav li.active').parent().addClass('collapse in');

</script>


 <!-- 用于加载js代码 -->
   <!-- /底部 -->
</body>
</html>
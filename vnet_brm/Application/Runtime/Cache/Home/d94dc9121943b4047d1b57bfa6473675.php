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

<style>
li{ list-style:none; margin:0; padding:0;}
.clear{ clear:both;}
.form-li{ width:100%; box-sizing:border-box; background:#f1f1f1; margin:10px 0; padding-top:10px; border-radius:6px 6px 0 0;}
.form-li li{ float:left; padding:0 30px; border-radius:6px 6px 0 0; border:1px solid #dbdbdb; line-height:34px; margin:0 4px; background:#f7f7f7; cursor:pointer;}
.form-li li.active{ border-bottom:1px solid #fff;  background:#fff;}
.form-li li:hover{ background:#888; color:#fff; }

.form-content .display{ display:none;}
.form-content .display.block{ display:block;}

.xiaobiaoti{ color:#95b8d6; margin-bottom:10px; border-bottom:2px solid #95b8d6; font-size:18px; font-weight:bold; line-height:34px; padding-left:10px;}
.hang-coco{ line-height:34px; text-align:center;}
.hang-biaoti{ background:#95b8d6; color:#fff; margin:4px auto; width:80%; font-size:16px; font-weight:bold;}
.hang-coco a{line-height:30px;  background:#f7f7f7; margin:1px auto; width:80%; display:block; color:#333;}
.hang-coco a:hover{ background:#dbdbdb; }
.checkbox-coco{ display:block; float:left; line-height:34px; font-size:16px;}
input[type="checkbox"].checkbox-coco-kuang{ margin:9px 6px 0 0; width:16px; height:16px;}

.coco-rizhi h4{ line-height:34px; font-size:14px;}

.sortable{ line-height:34px; margin-bottom:10px;}
.sortable th{background:#95b8d6; color:#fff; padding:0 4px; }
.sortable tr:nth-of-type(2n+1){ background:#f8f8f8;}
.sortable input[type="checkbox"]{ width:16px; height:16px; margin:0 0 0 6px;}

</style>


        <div class="header">
    	<!--page title begin-->
        <h1 class="page-title">硬件添加</h1>
        </div>
        <ul class="breadcrumb">
        	<!--  <li><a href="new_device.html">绑定新设备</a> <span class="divider">/</span></li>
            <li class="active">设备管理</li>-->
        </ul>
        <!--page title end and the main content begin-->
        <div class="container">
            <!--new device from begin-->
            <div class="row">
                <div class="col-xs-12">
                
                    <div class="display block">
                        <form action="" class="form-horizontal" id="add-device" role="form" method="post">
                        <div class="col-xs-4">
                        	<div class="xiaobiaoti">内在特性</div>	
                            <input type="hidden" name="flag" value="1">
                            <input type="hidden" name="id" value="<?php echo ($id); ?>">
                            <div class="form-group">
                                <label for="category-name" class="col-xs-4 control-label">硬件类型:</label>                            
                                <div class="col-xs-8">
                                    <select class="form-control" name="itemtypeid">
                                        <?php if(is_array($type_list)): $i = 0; $__LIST__ = $type_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option class="level-0" value="<?php echo ($vo["id"]); ?>"> <?php echo ($vo["typedesc"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-name" class="col-xs-4 control-label">厂商:</label>
                                <div class="col-xs-8">
                                    <select class="form-control" name="manufacturerid">
                            			<?php if(is_array($agent_list)): $i = 0; $__LIST__ = $agent_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option class='level-0' value='<?php echo ($vo["id"]); ?>'  ><?php echo ($vo["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="device-type" class="col-xs-4 control-label"><span style="color:red;">*</span>设备名称:</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="indate" name="common_name"  required="required">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="device-type" class="col-xs-4 control-label"><span style="color:red;">*</span>型号:</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="indate" name="model"  required="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-uuid" class="col-xs-4 control-label"><span style="color:red;">*</span>SN号:</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="indate" name="sn"  require required="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="device-type" class="col-xs-4 control-label">SN2号:</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="indate" name="sn2" value="" require>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-key" class="col-xs-4 control-label">资产编号:</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="start" name="asset"  >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-key" class="col-xs-4 control-label"><span style="color:red;">*</span>所占U数:</label>
                                <div class="col-xs-8">
                                    <input type="number" class="form-control" id="start" name="usize" required="required" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-key" class="col-xs-4 control-label">注释:</label>
                                <div class="col-xs-8">
                                    <textarea class="form-control" id="device-description" name="comments" rows="3" maxlength="150"></textarea>
                                </div>
                            </div>
                            
                            
                            <div class="xiaobiaoti">账目</div>	
                            
                            
                             <div class="form-group">
                                <label for="category-name" class="col-xs-4 control-label">采购商店名称:</label>                            
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="cai" name="origin"  require>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-name" class="col-xs-4 control-label">采购价格(￥):</label>                            
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="cai" name="purchprice"  require>
                                </div>
                            </div>
                           
                          
                           
                           	
                    	</div>
                    	
                    	<div class="col-xs-4">
                        	<div class="xiaobiaoti">维保</div>	
                           
                            
                            <div class="form-group">
                                <label for="category-name" class="col-xs-4 control-label">采购日期:</label>                            
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="startcai" name="purchasedate"  require>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-name" class="col-xs-4 control-label">保修时间(月):</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="cai" name="warrantymonths" require>
                                </div>
                            </div>
                            
                            <div class="xiaobiaoti">网络</div>	
                            
                            
                           <div class="form-group">
                                <label for="product-key" class="col-xs-4 control-label">DNS 名称:</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="ddd" name="dnsname" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-key" class="col-xs-4 control-label">MACs:</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="ddd" name="macs"  >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-key" class="col-xs-4 control-label">IPv4:</label>
                                <div class="col-xs-8">
                                   <input type="text" class="form-control" id="ddd" name="ipv4"  >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-key" class="col-xs-4 control-label">IPv6:</label>
                                <div class="col-xs-8">
                                   <input type="text" class="form-control" id="ddd" name="ipv6" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-name" class="col-xs-4 control-label">交换机:</label>                            
                                <div class="col-xs-8">
                                    <select class="form-control" name="switchid">
                                        <option class="level-0" value=""  >请选择交换机</option>
                                        <?php if(is_array($switch_list)): $i = 0; $__LIST__ = $switch_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option class="level-0" value="<?php echo ($vo["id"]); ?>"  ><?php echo ($vo["common_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-key" class="col-xs-4 control-label">端口:</label>
                                <div class="col-xs-8">
                                   <input type="text" class="form-control" id="start" name="ports" >
                                </div>
                            </div>
                            
                            <div class="xiaobiaoti">MISC</div>	
                            
                            <div class="form-group">
                                <label for="device-type" class="col-xs-4 control-label">内存 (GB):</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="indate" name="ram"  require>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="device-type" class="col-xs-4 control-label">CPU型号:</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="indate" name="cpu"  require>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="device-type" class="col-xs-4 control-label">设备功率:</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="indate" name="power" value="" require>
                                </div>
                            </div>
                           
                           	
                    	</div>
                    	
                    	<div class="col-xs-4">
                        	
                            <div class="xiaobiaoti">相关信息</div>	
                            
                            <div class="form-group">
                                <label for="device-type" class="col-xs-3 control-label"><span style="color:red;">*</span>所属部门:</label>
                                <div class="col-xs-8">
                                  <select class="form-control" name="depart_id" required="required">
                                   <?php if(is_array($depart_list)): $i = 0; $__LIST__ = $depart_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option class="level-0" value="<?php echo ($vo["id"]); ?>"  ><?php echo ($vo["depart_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="device-type" class="col-xs-3 control-label">所属产品:</label>
                                <div class="col-xs-8">
                                  <select class="form-control" name="belong_product">
                        
                                            <option class="level-0" value=""  >请选择所属产品</option>
                                        	<option class="level-0" value="BB产品"  >BB产品</option>
                                        	<option class="level-0" value="三线BGP"  <?php if('三线BGP' == $belong_product): ?>selected="selected"<?php endif; ?>>三线BGP</option>
                                    		<option class="level-0" value="全线BGP"  <?php if('全线BGP' == $belong_product): ?>selected="selected"<?php endif; ?>>全线BGP</option>
                                    		<option class="level-0" value="共享"  <?php if('共享' == $belong_product): ?>selected="selected"<?php endif; ?>>共享</option>
                                    		<option class="level-0" value="单电信"  <?php if('单电信' == $belong_product): ?>selected="selected"<?php endif; ?>>单电信</option>
                                    		<option class="level-0" value="单移动"  <?php if('单移动' == $belong_product): ?>selected="selected"<?php endif; ?>>单移动</option>
                                    		<option class="level-0" value="单线国际"  <?php if('单线国际' == $belong_product): ?>selected="selected"<?php endif; ?>>单线国际</option>
                                    		<option class="level-0" value="单联通"  <?php if('单联通' == $belong_product): ?>selected="selected"<?php endif; ?>>单联通</option>
                                    		<option class="level-0" value="双线"  <?php if('双线' == $belong_product): ?>selected="selected"<?php endif; ?>>双线</option>
                                    		<option class="level-0" value="双线BGP"  <?php if('双线BGP' == $belong_product): ?>selected="selected"<?php endif; ?>>双线BGP</option>
                                    		<option class="level-0" value="四线BGP"  <?php if('四线BGP' == $belong_product): ?>selected="selected"<?php endif; ?>>四线BGP</option>
                                    		<option class="level-0" value="小宽带"  <?php if('小宽带' == $belong_product): ?>selected="selected"<?php endif; ?>>小宽带</option>
                                    		<option class="level-0" value="静态四线BGP"  <?php if('静态四线BGP' == $belong_product): ?>selected="selected"<?php endif; ?>>静态四线BGP</option>
                                    		<option class="level-0" value="骨干网设备"  <?php if('骨干网设备' == $belong_product): ?>selected="selected"<?php endif; ?>>骨干网设备</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="device-type" class="col-xs-3 control-label">slot number:</label>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="indate" name="slot_number" value="" require>
                                </div>
                            </div>
                           
                           	
                    	</div>
                    	
                    	<div class="clear"></div>
                    	
                    	
                    	
                    	<div class="form-actions col-xs-8 pull-right">
                        		<input type="submit" class="btn btn-primary" id="other"></input>
                        		
                    		</div>
                        </form>
                    </div>
                
                
                
                    
                </div>
            </div>


            </div>
        <!--the content end -->
      
                    <footer>
                        <hr>
                        <p class="pull-right"></p>
                    </footer>
                    
</div>
   
    <script type="text/javascript" src="/vnet_brm/Public/static/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="/vnet_brm/Public/static/jedate/jedate.js"></script>
    <script type="text/javascript">
       
    //jeDate.skin('gray');
    jeDate({
    	dateCell:"#startcai",
        format:"YYYY-MM-DD hh:mm:ss",
        isTime:true
    })
    </script>
   


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
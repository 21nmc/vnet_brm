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
      <!--page title begin-->
        <h1 class="page-title">设备信息</h1>
        </div>
       
        <!--page title end and the main content begin-->

        <div class="container">

          <!--the toolbar begin -->
            <div class="toolbar">
                <div class="row">
                    <div class="col-xs-6" style="width:100%;">
                        <div class="quick-add-btn">

                            <a href="<?php echo U('item_edit?id='.$id);?>"  type="button" class="btn btn-danger">
                            <span class="glyphicon glyphicon-new-window"></span>设备编辑</a>
                            <a href="<?php echo U('item_log?id='.$id);?>"  type="button" class="btn btn-danger">
                            <span class="glyphicon glyphicon-new-window"></span>设备Log</a>

                       </div>
                    </div>
                </div>
            </div>
        <!--all device detail begin -->
        <?php if($flag == 'on'): ?><table width="100%">
        <tr>
        <form action="" class="form-horizontal" id="add-device" role="form" method="post" >
              <input type="hidden" name="objectid" value="<?php echo ($id); ?>">
        
        <td valign="top">
                  <table class="rack" width="80%">
                    <tbody>

                      <tr><th >RU</th><th>前</th><th>中</th><th>后</th></tr>

                      <?php echo ($racktable); ?>


                      <tr><td colspan="4" style="background-color:#666;border:1px solid #666;padding:0;">&nbsp;</td></tr>
                      <tr>
                      <td style="border:0"></td><td style="padding:0;border:0;text-align:center"><img height="30" src="/vnet_brm/Public/images/rackwheel.png"></td>
                      <td style="border:0"></td><td style="padding:0;border:0;text-align:center"><img height="30" src="/vnet_brm/Public/images/rackwheel.png"></td>
                      </tr>

                    </tbody>
                  </table>
       </td>
       
       
                    <td valign="top">
                      <div class="portlet">
                          <h2>汇总</h2>
                          <table border="0" cellspacing="0" cellpadding="3" width="100%">
                              <tbody>
                                  <tr>
                                      <th width="50%" class="tdright">机房-房间:</th>
                                      <td class="tdleft"><?php echo ($location_name); ?>-<?php echo ($areaname); ?></td>
                                  </tr>
                                  <tr>
                                      <th width="50%" class="tdright">机柜:</th>
                                      <td class="tdleft"><?php echo ($rackcomments); ?></td>
                                  </tr>
                                  <tr>
                                      <th width="50%" class="tdright">设备名:</th>
                                      <td class="tdleft"><?php echo ($common_name); ?></td>
                                  </tr>
                                  <tr>
                                      <th width="50%" class="tdright">设备U大小:</th>
                                      <td class="tdleft"><?php echo ($myusize); ?>U</td>
                                  </tr>
                                   <tr>
                                      <th width="50%" class="tdright">设备状态:</th>
                                      <td class="tdleft"><?php echo ($status); ?>&nbsp;&nbsp;</td>
                                  </tr>
                                  <tr>
                                      <th width="50%" class="tdright">设备类型:</th>
                                      <td class="tdleft"><?php echo ($typedesc); ?></td>
                                  </tr>
                                  <tr>
                                      <th width="50%" class="tdright">Ip:</th>
                                      <td class="tdleft"><?php echo ($ip); ?></td>
                                  </tr>
                                  <tr>
                                      <th width="50%" class="tdright sticker">OEM S/N:</th>
                                      <td class="tdleft"><?php echo ($sn); ?></td>
                                  </tr>
                                  <tr>
                                      <th width="50%" class="tdright sticker">操作系统:</th>
                                      <td class="tdleft"><a href="javascript:void(0)"><?php echo ($system); ?>关联软件</a></td>
                                  </tr>
                                  <tr>
                                      <th width="50%" class="tdright sticker">设备厂商/品牌:</th>
                                      <?php  $pinpai = $angnt_list["$manufacturerid"]; ?>
                                      <td class="tdleft"><a href="javascript:void(0)"><?php echo ($pinpai); ?></a></td>
                                  </tr>
                                  <tr>
                                      <th width="50%" class="tdright sticker">设备型号:</th>
                                      <td class="tdleft"><a href="javascript:void(0)"><?php echo ($model); ?></a></td>
                                  </tr>
                                  <tr>
                                      <th width="50%" class="tdright sticker">设备所属部门:</th>
                                      <td class="tdleft"><a href="javascript:void(0)"><?php echo ($dname); ?></a></td>
                                  </tr>

                              </tbody>
                          </table>
                      </div>

                      <div class="portlet">
                          <h2>Comment</h2>
                          <div class="commentblock"><?php echo ($comments); ?></div>
                      </div>

                    </td>


          </tr>
        </table>
       
       
        </form>
       <?php else: ?>

        <table width="100%">
          <tr>
                <td valign="top">
                  <div class="portlet">
                      <h2><b>信息</b></h2>
                      <table border="0" cellspacing="0" cellpadding="3" width="100%">
                          <tbody>
                              <?php echo ($item_text); ?>



                          </tbody>
                      </table>
                  </div>
                </td>
          </tr>
        </table><?php endif; ?>




        </div>
        

                    
    </div>
    
 <script type="text/javascript">

  function p_del() { 
    var msg = "您真的确定要执行删除操作吗？\n\n请确认！"; 
    if (confirm(msg)==true){ 
        return true; 
    }else{ 
        return false; 
    } 
  } 



</script>  
   
  <SCRIPT type="text/javascript">
    
    var setting = {
      data: {
        simpleData: {
          enable: true
        }
      }
    };



    var zNodes = <?php echo ($json_showmenu); ?>;

    $(document).ready(function(){
      $.fn.zTree.init($("#treeDemo"), setting, zNodes);
      $(".checkinput").click(function(){
        if(!$(this).hasClass('disabled')){
            var input = $(this).parent().find('input');
            var num = 0;
            for(var i=0;i<input.length;i++){
                if(input[i].checked){
                    num++;
                }
            }   
            if(num==3){
                for(var i=0;i<input.length;i++){
                    input[i].checked = false;
                }  
            }else{
                for(var i=0;i<input.length;i++){
                    input[i].checked = true;
                }
            }
        }
      });
    });
    
  </SCRIPT>










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
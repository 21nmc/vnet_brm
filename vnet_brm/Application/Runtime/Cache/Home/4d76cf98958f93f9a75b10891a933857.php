<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!-- saved from url=(0036)http://user.oneapm.com/pages/v2/home -->
<html class="no-js" lang="zh-CN">
<!--<![endif]-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>查询资产</title>
    <meta name="viewport" content="target-densitydpi=device-dpi,width=640,user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- 国产双核浏览器用 start -->
    <meta name="renderer" content="webkit">
    <!-- 国产双核浏览器用 end -->
    <link rel="shortcut icon" href="">
    <meta name="msapplication-TileColor" content="#da532c">
    <!--link rel="stylesheet" href="./css/account.css"-->
    <script src="/vnet_brm/Public/lib/jquery-1.7.2.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="/vnet_brm/Public/static/ly/reset.css">
    <link rel="stylesheet" type="text/css" href="/vnet_brm/Public/static/ly/mobile.css">
</head>

<body>
    <div id="layout">
        <div class="header">
            <div class="logo">
                <a href="<?php echo U('Mobile/index');?>"><img src="/vnet_brm/Public/images/logo.png"></a></div>
            <div class="account">
                <strong class="account-info">
            <span><a href="<?php echo U('Login/logout');?>" onclick="return confirm('确定退出本系统？')"><?php echo ($username); ?></a></span>
          </strong>
            </div>
        </div>
        <div class="wrap clearfix">
            <div class="content no-sidebar " id="root">
                <div class="home-list">
                    <div class="dismaptitle">
                        <div class="map1title">查询资产</div><div class="mapment"><a href="<?php echo U('Mobile/index');?>">首页</a>-><a href="<?php echo U('Mobile/scan');?>">条形、二维码扫描</a>-><a href="<?php echo U('Mobile/findscan');?>" >查询资产</a></div>
                    </div>
                    <div class="mapbox">
                        <div class="chatitle">扫描框</div>
                        <div class="mapsearchbox">
                          <form name="form1" method="post">
                            <div class="clashang"><input type="text" name="sn" required></div>
                            <div class="clashang2"><button >查询</button><button type="reset">重新扫描</button></div>
                          </form>
                        </div>

                        <div class="chatitle">查询结果</div>
                        <table class="table-striped">


                            <?php if(empty($list)): ?><tr><td colspan="12" class="text-center"> <center>记录为空! </center></td></tr>
                            <?php else: ?>
                                <tr>
                                    <th>名称</th>
                                    <th>数据中心/房间/机柜</th>
                                </tr>
                                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                        <td><?php echo ($vo["common_name"]); ?></td>
                                        <td>
                                            <a href="<?php echo U('Ipdb/viewlocation?locationid='.$vo['locationid']);?>"><?php $lid = $vo['locationid'];echo $location_list[$lid];?></a>
                                            /<a href="<?php echo U('Ipdb/viewarea?areaid='.$vo['locareaid']);?>"><?php $rid = $vo['locareaid'];echo $area_list[$rid];?></a>
                                            /<a href="<?php echo U('Racks/viewrack?rackid='.$vo['rackid']);?>"><?php $rackid = $vo['rackid'];echo $rack_list[$rackid];?>
                                            </a>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>

                        </table>



                </div>
            </div>
        </div>
        <div id="layout_footer"></div>
    </div>
    <div class="footer">
        <ul>
            <!--li class="divider"></li-->
            <li>
                <p>世纪互联 共享网络中心-系统部</p>
            </li>
        </ul>
    </div>
    <script>
    $(document).ready(function() {
        //鼠标移入变红色  

        $(".pure-u-1-2 .box").mouseover(function() {
            //alert('111');
            $(this).find(".seee").removeClass('none');


        }).mouseleave(function() {
            $(this).find(".seee").addClass('none');
            //$("#jichunow").removeClass('none');
        });
    });
    </script>
</body>

</html>
<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!-- saved from url=(0036)http://user.oneapm.com/pages/v2/home -->
<html class="no-js" lang="zh-CN">
<!--<![endif]-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>九宫格</title>
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
                    <div class="pure-g twobox">
                        <div class="pure-u-1-2 pure-u-md-1-3 pure-u-lg-1-4">
                            <div class="box" id="jichunow">
                                <a data-id="10" data-key="ai" class="product-link ai" target="_blank"  href="<?php echo U('Mobile/map1');?>">
                                    <div class="icon-box clearfix ps-relative">
                                        <div class="icon-bg">
                                            硬件 </div>
                                            <div class="icon_img"><img src="/vnet_brm/Public/images/screen_83.612903225806px_1192859_easyicon.net.png"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="pure-u-1-2 pure-u-md-1-3 pure-u-lg-1-4">
                            <div class="box">
                                <a data-id="10" data-key="bi" href="http://127.0.0.1/vnet_brm/index.php/Home/Ipdb/listareas.html" class="product-link bi" target="_blank">
                                    <div class="icon-box clearfix ps-relative">
                                        <div class="icon-bg">
                                            软件 </div>
                                            <div class="icon_img"><img src="/vnet_brm/Public/images/screen_83.612903225806px_1192859_easyicon.net.png"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

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
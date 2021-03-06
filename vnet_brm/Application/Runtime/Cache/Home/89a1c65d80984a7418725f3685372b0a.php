<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!-- saved from url=(0036)http://user.oneapm.com/pages/v2/home -->
<html class="no-js" lang="zh-CN">
<!--<![endif]-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>九宫格</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- 国产双核浏览器用 start -->
    <meta name="renderer" content="webkit">
    <!-- 国产双核浏览器用 end -->
    <link rel="shortcut icon" href="">
    <meta name="msapplication-TileColor" content="#da532c">

    <!--link rel="stylesheet" href="./css/account.css"-->

    <script src="/vnet_brm/Public/lib/jquery-1.7.2.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="/vnet_brm/Public/static/ly/account.css">
    <style>
        .box{ padding: 0; margin:15px;}
        .sel_btn{
            height: 21px;
            line-height: 21px;
            padding: 0 11px;
            background: #02bafa;
            border: 1px #26bbdb solid;
            border-radius: 3px;
            /*color: #fff;*/
            display: inline-block;
            text-decoration: none;
            font-size: 12px;
            outline: none;
        }
       .none{ display:none;}
        .box{ position:relative;}
        .seee{ position:absolute;width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 99;
    border-radius: 6px;    
    box-sizing: border-box;
    padding-top: 30px;}
        .seee .button-a,.seee .button-input{ display:block; border:none; margin:10px auto; box-sizing:content-box; background:rgba(250,250,250,0.8);color:#000; height:30px; border-radius:4px; text-align:center; line-height:30px; width: 80%;}
        .seee .button-a:hover,.seee .button-input:hover{ background:rgba(250,250,250,1);}
</style>
    </style>

<script type="text/javascript">
    function deal_tiki(){
        var tikiurl = "<?php echo ($tikiurl); ?>";
        window.open(tikiurl);return;
    }

</script>


</head>

<body>
    <div id="layout">
        <div class="header">
            <div class="logo">
                <!--a class="iconfont icon-oneapmlogo" href="" title="">
            <span>111</span>
          </a-->
                <!-- <a class="iconfont icon-oneapmlogo" href="" title="">
            <span></span>
          </a> -->
            </div>
            <div class="account">
                <strong class="account-info">
            <span><a href="<?php echo U('Login/logout');?>" onclick="return confirm('确定退出本系统？')"><?php echo ($username); ?></a></span>
          </strong>
            </div>

            <!--div class="order">
                <strong class="order-title">
            <i class="iconfont"></i>
            <span>工单</span>
            <i class="iconfont icon-arrowup"></i>
          </strong>

            </div-->
        </div>
        <div class="wrap clearfix">
            <div class="content no-sidebar " id="root">
                <div class="home-list">


                    <div class="pure-g">
                        <?php if(is_array($list2)): $i = 0; $__LIST__ = $list2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; $cssname = $vo['cssname']; ?>
                        <div class="pure-u-1-2 pure-u-md-1-3 pure-u-lg-1-4"   >
                             
                            <div class="box"  <?php if($cssname =='ai'){ ?> id="jichunow" <?php } ?>>
                              
                                 <?php if($cssname == 'ai'){ $url=$vo['url']; ?>
                                    <div class="none seee"><a class="button-a" href="http://127.0.0.1/vnet_brm/index.php/Home/Item/index.html" target="_blank">硬件</a>
                                    <a class="button-a" href="http://127.0.0.1/vnet_brm/index.php/Home/Soft/index.html" target="_blank" >软件</a>
                                    </div>
                                 <?php } ?>



                                <?php if($cssname == 'alert'){ $url=$vo['url']; ?>
                                    <div class="none seee"><a class="button-a" href="http://127.0.0.1/vnet_brm/index.php/Home/Racksreport/reportmap.html" target="_blank">Map报表</a>
                                        
                                    <a class="button-a" href="http://127.0.0.1/vnet_brm/index.php/Home/Racks/rackmap.html" target="_blank" >机柜报表</a>
                                    </div>
                                <?php } ?>


                                      
                          
                              <?php if($vo['url'] == '' ): ?><a data-id="10" data-key="<?php echo ($vo["cssname"]); ?>"  <?php if($cssname != 'ai'){ ?>href="<?php echo ($vo["url"]); ?>"<?php } ?> class="product-link <?php echo ($vo["cssname"]); ?>" target="_blank" >

                              <?php else: ?>
                                  <a data-id="10" data-key="<?php echo ($vo["cssname"]); ?>"    <?php if($cssname != 'ai'){ ?>href="<?php echo ($vo["url"]); ?>"<?php } ?> class="product-link <?php echo ($vo["cssname"]); ?>" target="_blank"><?php endif; ?>


                                    <div class="icon-box clearfix ps-relative">
                                        <div class="icon-right ps-absolute pay-type">
                                            <!--p class="version pro-try">未开通</p-->
                                        </div>
                                        <div class="icon-bg">
                                            <?php echo ($vo["title"]); ?>
                                        </div>
                                    </div>
                                    <div class="text-box">
                                        <p class="desp"><?php echo ($vo["content"]); ?></p>
                                    </div>
                                </a>
                            </div>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>


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
    $(document).ready(function () {  
        //鼠标移入变红色  
        
        $(".pure-u-1-2 .box").mouseover(function (){  
            //alert('111');
            $(this).find(".seee").removeClass('none');
            
              
        }).mouseleave(function () {  
            $(this).find(".seee").addClass('none');
            //$("#jichunow").removeClass('none');
        });  
    });
    </script>
    
</body>

</html>
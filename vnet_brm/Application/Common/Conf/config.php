<?php
return array(
	/* 数据库配置 */
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'vnet_bmr', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'mysql',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'vnet_', // 数据库表前缀
    'LOG_RECORD' => false, // 默认不记录日志
    'SHOW_ERROR_MSG' => true,
   'SHOW_PAGE_TRACE' =>true, 
    
    'MODULE_DENY_LIST'   => array('Common'),
    /* 用户相关设置 */
    'USER_ADMINISTRATOR' => array(1,17,18,7,5), //管理员用户ID  数组
    
    'DB_CONFIG1' => 'mysql://root:mysql@211.151.5.18:3306/zabbix32',


);


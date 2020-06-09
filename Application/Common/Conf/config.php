<?php
return array(
	//'配置项'=>'配置值'
//    'DB_TYPE'               =>  'mysqli',     // 数据库类型
//    'DB_HOST'               =>  'localhost', // 服务器地址
//    'DB_NAME'               =>  'tlh',          // 数据库名
//    'DB_USER'               =>  'root',      // 用户名
//    'DB_PWD'                =>  '123456',          // 密码
//    'DB_PORT'               =>  '3306',        // 端口
//    'DB_PREFIX'             =>  '',    // 数据库表前缀
//    'DB_CHARSET'            =>  'utf8mb4',      // 数据库编码默认采用utf8

    //默认动作和控制器的设置
    'DEFAULT_MODULE'        =>  'Home',  // 默认模块
    'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'index', // 默认操作名称

    //页面trace功能
    'SHOW_PAGE_TRACE'       =>   false,

    //开启路由模式
    'URL_ROUTER_ON'   => true,

    //开启url模式
    'URL_MODEL'             =>   0,

    //url大小写敏感设置
    'URL_CASE_INSENSITIVE'  =>  true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
);
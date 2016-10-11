<?php
/*---------------------------------------------------------------------
 * 命令行任务入口文件
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 * @version 1.2.1
 *-----------------------------------------------------------------------*/
//设置页面编码
header("Content-Type:text/html; charset=utf-8");

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);
define('RUN_CLI', true);

// 定义当前访问的应用
define('APP_NAME', basename(__DIR__));

// 定义系统根目录
define('APP_ROOT', dirname(dirname(__DIR__)) . '/');

define('SERVER_NODE_NAME', 'server_node_1'); //当前服务器节点，分布式部署时需要使用
define('APP_PATH', APP_ROOT.'www/'.APP_NAME.'/'); //当前应用根目录

//定义框架根目录
define('APP_FRAME_PATH', APP_ROOT.'framework/herosphp/');

//包含系统框架的统一入口文件
require APP_FRAME_PATH . 'Herosphp.class.php';

//接收命令行参数
$taskName = $_SERVER['argv'][1];

//启动应用程序
Herosphp::runClient($taskName);

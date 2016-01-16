<?php
/*---------------------------------------------------------------------
 * 应用程序入口文件
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

// 定义当前访问的应用
define('APP_NAME', 'app');

// 定义系统根目录
define('APP_ROOT', __DIR__.'/');

//定义框架根目录
define('APP_FRAME_PATH', APP_ROOT.'framework/herosphp/');

//包含系统框架的统一入口文件
require APP_FRAME_PATH.'Herosphp.class.php';

//启动应用程序
Herosphp::run();

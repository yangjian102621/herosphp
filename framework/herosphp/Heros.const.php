<?php
/*---------------------------------------------------------------------
 * 系统常量定义
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/
//定义编译路径
define('APP_RUNTIME_PATH', APP_ROOT.'runtime/');
//定义配置文档路径
define('APP_CONFIG_PATH', APP_ROOT.'configs/');

define('RES_PATH', APP_ROOT.'res/');    //静态资源的根目录
define('EXT_PATH', APP_ROOT.'extend/');    //外部扩展库根目录
define('RES_URL', '/res/');     //静态资源的根url

//定义时区
define('TIME_ZONE', 'PRC');

//定义import加载类的类别
define('IMPORT_APP', 1);    //加载当前应用模块中的class
define('IMPORT_CLIENT', 2);    //加载客户端脚本的class
define('IMPORT_FRAME', 3);  //加载框架中的class
define('IMPORT_CUSTOM', 4); //加载自定义路径中的class

define('EXT_PHP', '.class.php');   //加载php文件
define('EXT_MODEL', '.model.php');   //加载model文件
define('EXT_HTML', '.html');    //加载html文件

define('EXT_TPL', '.html');     //模板文件后缀
define('EXT_URI', '.shtml');     //uri 伪静态路径后缀

/**
 * 以下定义数据库类别
 */
define('DB_TYPE_MYSQL', 1);     //mysql数据库
define('DB_TYPE_POSTGRE', 2);   //PostgreSQL数据库
define('DB_TYPE', DB_TYPE_MYSQL);   //默认使用mysql数据库

/**
 * 以下订制数据库访问策略，提供单台服务器访问和读写分离集群访问模式
 * 如果采用集群访问模式请在 /config/db/host.config.php文件中设置好你的数据库集群配置
 */
define('DB_ACCESS_SINGLE', 1);  //单台服务器访问模式
define('DB_ACCESS_CLUSTERS', 2);  //数据库服务器集群调度访问模式
define('DB_ACCESS', DB_ACCESS_SINGLE);  //默认使用单台数据库服务器

/**
 * 定义session介质
 * 1. file => 文件介质存储 (default)
 * 2. memo => memcache介质存储
 */
define('SESSION_HANDLER', 'file');

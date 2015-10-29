<?php
/*---------------------------------------------------------------------
 * 当前访问application配置信息.
 * 注意：此处的配置将会覆盖同名键值的系统配置
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

$config = array(

    'site_url' => 'http://blog.fiidee.my',     //网站地址
    'domain' => 'blog.fiidee.my',     //网站域名
    'res_url' => 'http://blog.fiidee.my',      //静态资源的服务器地址(css, image)
    //默认访问的页面
    'default_url' => array(
        'module' => 'admin',
        'action' => 'login',
        'method' => 'index' ),

    'template' => 'default',    //默认模板
    'skin' => 'default',    //默认皮肤
    'temp_cache' => 1,      //模板引擎缓存


    //应用信息配置
    'admin_title' => "FiiDee Blog 后台管理",      //后台Project name

    //系统配置分组
    'system.config.group' => array(
        'basic' => '基础配置',
        'email' => '邮件配置',
    ),
);

return $config;
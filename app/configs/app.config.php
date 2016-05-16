<?php
/*---------------------------------------------------------------------
 * 当前访问application配置信息.
 * 注意：此处的配置将会覆盖同名键值的系统配置
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

$config = array(

    'site_url' => 'http://www.herosphp.my',     //网站地址
    'domain' => 'http://www.herosphp.my',     //网站域名
    'res_url' => 'http://www.herosphp.my',      //静态资源的服务器地址(css, image)
    //默认访问的页面
    'default_url' => array(
        'module' => 'test',
        'action' => 'index',
        'method' => 'index' ),

    'template' => 'default',    //默认模板
    'temp_cache' => 0,      //模板引擎缓存

    'site_name' => 'HerosPHP 快速开发平台',
    'site_copyright' => '2016 &copy; HerosPHP by BlackFox',

);

return $config;
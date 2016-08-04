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

    'template' => 'default',    //默认模板
    /**
     * 模板编译缓存配置
     * 0 : 不启用缓存，每次请求都重新编译(建议开发阶段启用)
     * 1 : 开启部分缓存， 如果模板文件有修改的话则放弃缓存，重新编译(建议测试阶段启用)
     * -1 : 不管模板有没有修改都不重新编译，节省模板修改时间判断，性能较高(建议正式部署阶段开启)
     */
    'temp_cache' => 0,

    /**
     * 用户自定义模板标签编译规则
     * array( 'search_pattern' => 'replace_pattern'  );
     */
    'temp_rules' => array(),

    'site_url' => 'http://www.herosphp.my',     //网站地址
    'domain' => 'http://www.herosphp.my',     //网站域名
    'res_url' => 'http://www.herosphp.my',      //静态资源的服务器地址(css, image)
    //默认访问的页面
    'default_url' => array(
        'module' => 'test',
        'action' => 'demo',
        'method' => 'index' ),

    'template' => 'default',    //默认模板
    'temp_cache' => 0,      //模板引擎缓存

    'site_name' => 'HerosPHP 快速开发平台',
    'site_copyright' => '2016 &copy; HerosPHP by BlackFox',

);

return $config;
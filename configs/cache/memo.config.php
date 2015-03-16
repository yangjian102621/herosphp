<?php
/**
 * memory cache 缓存配置
 * @author yangjian <yangjian102621@163.com>
 */
return array(
    //缓存服务器
    'server' => array(
        //Memcahe服务器1, 服务器域名 => 服务器端口
        array('localhost', 11211)
        //Memcache服务器2
        /*, array('www.webssky.com',11211)*/
    ),
    //缓存生命周期
    'expire' => 60*60*2
);
?>

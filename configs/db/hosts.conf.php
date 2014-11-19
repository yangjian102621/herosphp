<?php
/*---------------------------------------------------------------------
 * 数据库连接配置文件
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

return array(
    //主数据库配置
    'main'      => array(
        'host'      => 'localhost',
        'port'      => 3306,
        'user'      => 'root',
        'pass'      => '123456',
        'db'        => 'herosphp',
        'charset'   => 'utf8',
        'id'        => 'main-db',
    ),
    
    //mongo DB 数据库配置
    'mongo'     => array(
        'host'      => '192.168.1.119',
        'port'      => 27017,
        'user'      => 'root',
        'pass'      => '123456',
        'db'        => 'herosphp'
    )
);
?>
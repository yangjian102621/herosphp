<?php
/*---------------------------------------------------------------------
 * 数据库操作工厂类,创建数据库连接对象
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 * @version 1.2.1
 *-----------------------------------------------------------------------*/

namespace herosphp\db;

use herosphp\core\Loader;
use herosphp\db\interfaces\Idb;

class DBFactory {

    /**
     * 数据库连接池
     * @var array
     */
    private static $DB_POOL = array();

    /**
     * 数据库驱动配置
     * @var array
     */
    private static $DB_DRIVER = array(
        //单台服务器
        DB_ACCESS_SINGLE => array(
            'path' => 'db.mysql.SingleDB',
            'class' => 'herosphp\db\mysql\SingleDB'
        ),
        //服务器集群
        DB_ACCESS_CLUSTERS => array(
            'path' => 'db.mysql.ClusterDB',
            'class' => 'herosphp\db\mysql\ClusterDB'
        ),
        //mongodb
        'mongo' => array(
            'path' => 'db.mongo.MongoDB',
            'class' => 'herosphp\db\mongo\MongoDB'
        ),
    );

    /**
     * 创建数据库连接实例
     * @param int $accessType   连接方式（连接单个服务器还是连接集群）
     * @param array $config 数据库的配置信息
     * @return Idb
     */
    public static function createDB( $accessType=DB_ACCESS_SINGLE, &$config = null ) {

        //获取包含路径
        $classPath = self::$DB_DRIVER[$accessType]['path'];
        Loader::import($classPath, IMPORT_FRAME);
        $className = self::$DB_DRIVER[$accessType]['class'];
        $key = md5($className.$config['flag']);
        if ( !isset(self::$DB_POOL[$key]) ) {
            self::$DB_POOL[$key] = new $className($config);
        }
        return self::$DB_POOL[$key];
	}

}
?>

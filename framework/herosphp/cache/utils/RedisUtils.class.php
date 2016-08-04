<?php

namespace herosphp\cache\utils;

/*---------------------------------------------------------------------
 * redis操作工具类
 * @package herosphp\utils
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

use herosphp\core\Loader;

class RedisUtils {

    //redis实例
    private static $redis = null;

    private function __construct(){}

    /**
     * @return bool|null|\Redis
     */
    public static function getInstance() {

        if ( is_null(self::$redis) ) {
            $configs = Loader::config('redis', 'cache');
            if ( !$configs ) return false;
            self::$redis = new \Redis();
            self::$redis->connect($configs['host'], $configs['port']);
        }
        return self::$redis;
    }

} 
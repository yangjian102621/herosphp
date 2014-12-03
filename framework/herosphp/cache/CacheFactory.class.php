<?php
/*---------------------------------------------------------------------
 * 缓存实例化工厂类(缓存集合set)
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\cache;

use herosphp\core\Loader;

Loader::import('cache.ACache', IMPORT_FRAME);
Loader::import('cache.interfaces.ICache', IMPORT_FRAME);
class CacheFactory {

    private static $CACHE_SET = array();

    private static $CACHE_BEAN = array(
        'file' => array(
            'class' => 'herosphp\cache\FileCache',
            'path' => 'cache.FileCache'
        ),
        'html' => array(
            'class' => 'herosphp\cache\HtmlCache',
            'path' => 'cache.HtmlCache'
        ),
        'memo' => array(
            'class' => 'herosphp\cache\MemoCache',
            'path' => 'cache.MemoCache'
        )
    );

    /**
     * 创建缓存
     * @param $key
     * @param array $configs
     * @return \herosphp\cache\interfaces\ICache
     */
    public static function  create( $key, $configs = null ) {
        //如果缓存对象已经创建，则则直接返回
        if ( isset(self::$CACHE_SET[$key]) ) {
            return self::$CACHE_SET[$key];
        }

        if ( $configs == null ) {
            $configs = Loader::config($key, 'cache');
        }
        $className = self::$CACHE_BEAN[$key]['class'];
        Loader::import(self::$CACHE_BEAN[$key]['path'], IMPORT_FRAME);
        self::$CACHE_SET[$key] = new $className($configs);
        return self::$CACHE_SET[$key];

    }
}
?>
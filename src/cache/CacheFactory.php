<?php
/**
 * 缓存实例化工厂类(缓存集合set)
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\cache;

use herosphp\core\Loader;

class CacheFactory {

    private static $CACHE_SET = array();

    private static $CACHE_BEAN = array(
        'file' => array(
            'class' => 'herosphp\cache\FileCache',
            'path' => 'cache.FileCache'
        ),
        'redis' => array(
            'class' => 'herosphp\cache\RedisCache',
            'path' => 'cache.RedisCache'
        ),
        'memo' => array(
            'class' => 'herosphp\cache\MemoCache',
            'path' => 'cache.MemoCache'
        )
    );

    /**
     * 创建缓存
     * @param $key
     * @param bool $single 是否单例模式
     * @return \herosphp\cache\interfaces\ICache
     */
    public static function  create( $key='file', $single = true ) {
        //如果缓存对象已经创建，则则直接返回
        if ( isset(self::$CACHE_SET[$key]) && $single == false ) {
            return self::$CACHE_SET[$key];
        }

        $configs = Loader::config($key, 'cache');   //加载缓存配置
        $className = self::$CACHE_BEAN[$key]['class'];
        Loader::import(self::$CACHE_BEAN[$key]['path'], IMPORT_FRAME);
        if ( $single ) {
            self::$CACHE_SET[$key] = new $className($configs);
            return self::$CACHE_SET[$key];
        } else {
            return $className($configs);
        }

    }
}

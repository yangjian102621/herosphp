<?php
/**
 * 缓存实例化工厂类(缓存集合set)
 * --------------------------------------------------------
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。 
 * ----------------------------------------------------------
 * @author 	yangjian<yangjian102621@gmail.com>
 * @completed	2013-04-09
 */

include __DIR__.DIR_OS.'ICache.class.php';
include __DIR__.DIR_OS.'ACache.class.php';

class CacheFactory {

    protected static $fileCache = NULL;     /* 文件缓存 */

    protected static $htmlCache = NULL;     /* HTML缓存 */

    protected static $memCache = NULL;      /* memory cache 缓存 */

    /**
     * 获取文件缓存对象
     * @param       $config     缓存配置参数
     * @return      FileCache
     */
    public static function getFileCache( $config )
    {
        if ( self::$fileCache == NULL ) {
            include __DIR__.DIR_OS.'FileCache.class.php';
            self::$fileCache = new FileCache($config);
        }
        return self::$fileCache;
    }

    /**
     * @param       $config     缓存配置参数
     * @return      HtmlCache
     */
    public static function getHtmlCache( $config )
    {
        if ( self::$htmlCache == NULL ) {
            include __DIR__.DIR_OS.'HtmlCache.class.php';
            self::$htmlCache = new HtmlCache($config);
        }
        return self::$htmlCache;
    }

    /**
     * @param       $config     缓存配置参数
     * @return      MemoCache
     */
    public static function getMemCache( $config )
    {
        if ( self::$memCache == NULL ) {
            include __DIR__.DIR_OS.'MemoCache.class.php';
            self::$memCache = new MemoCache($config);
        }

        return self::$memCache;
    }
}
?>
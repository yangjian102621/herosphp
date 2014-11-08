<?php
/**
 * HTML缓存生成类, 实现ICache接口
 * The class to make HTML file(cache). this should implements ICache.
 * ---------------------------------------------
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。 
 * ----------------------------------------------------------
 * @author 	yangjian<yangjian102621@gmail.com>
 * @version 1.0
 * @completed	2013-04-9
 * @lastupdate	2013-04-11
 */
class HtmlCache extends ACache implements ICache {

    /**
     * 初始化缓存系统
     * @param       $config
     */
	public function __construct( $config ) {
	    $this->config = $config;
	}

	/**
     * get cache content from cache file or memcache.
     * @see        ICache::get()
     */
    public function get( $_key, $_ctime ) {

        $_cache_file = $this->getCacheFile($_key).'.html';
        //缓存文件不存在
        if ( !file_exists($_cache_file) ) {
            Debug::appendMessage("Fail to get cache, cache file [{$_cache_file}] are not exists.");
            return FALSE;
        }
        //缓存过期, 若ctime = -1 则表示缓存永不过期
        if ( $this->config['ctime'] >= 0 && time() > (filemtime($_cache_file) + $this->config['ctime']) ) {

            Debug::appendMessage("The cache file [{$_cache_file}] is not valid.(缓存过期)");
            return FALSE;

        } else {

            return file_get_contents($_cache_file);;
        }
    }
    
    
    /**
     * @see        ICache::set();
     */
    public function set( $_key, $_content ) {

        $_cache_file = $this->getCacheFile($_key).'.html';
        
        return file_put_contents($_cache_file, $_content, LOCK_EX);
    }

    /**
     * delete cache
     * @see     interface ICache.delete
     */
    public function delete( $_key ) {

        $_cache_file = $this->getCacheFile($_key).'.html';
        return @unlink($_cache_file);
    }
}
?>
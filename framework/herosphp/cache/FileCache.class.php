<?php
/**
 * PHP文件缓存, 可用于缓存数据库的查询结果, 或者是对页面的局部缓存, 实现ICache接口
 * PHP file cache, can be used to cache the database query results, or local cache of page.
 * ---------------------------------------------
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。 
 * ----------------------------------------------------------
 * @author 	yangjian<yangjian102621@gmail.com>
 * @version  1.0
 * @completed	2013-04-10
 */

class FileCache extends ACache implements ICache {

	/**
	 *  初始化缓存配置
	 * @param		array		$config
	 */
	public function __construct( $config ) {
	    $this->config = $config;
	}
	
	/**
	 * get cache content from cache file or memcache.
	 * @see        ICache::get()
	 */
	public function get( $_key, $_ctime ) {

	    $_cache_file = $this->getCacheFile($_key);

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

			return file_get_contents($_cache_file);
		}
	}
	
	
	/**
	 * @see        ICache::set();
	 */
	public function set( $_key, $_content ) {
	    
        $_cache_file = $this->getCacheFile($_key);
		return file_put_contents($_cache_file, $_content, LOCK_EX);
	}
	
	/**
	 * delete cache
	 * @see		interface ICache.delete
	 */
	public function delete( $_key ) {

        $_cache_file = $this->getCacheFile($_key);
		return @unlink($_cache_file);
	}

}
?>
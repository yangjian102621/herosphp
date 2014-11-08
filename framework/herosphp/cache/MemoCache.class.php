<?php
/**
 * PHP文件缓存, 可用于缓存数据库的查询结果, 或者是对页面的局部缓存, 实现ICache接口
 * PHP file cache, can be used to cache the database query results, or local cache of page.
 * ---------------------------------------------
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。 
 * ----------------------------------------------------------
 * @author 	yangjian<yangjian102621@gmail.com>
 * @version 1.0
 * @completed	2013-04-10
 * @lastupdate	2013-04-11
 */
class MemoCache implements ICache {

	/* the instance of Memcache class */
	private static $Mem = NULL;
	
	private $_key = '';
	
	//缓存有效期，默认2小时
	private $_ctime = 7200;

	/**
	 * start and initialization the  Memcache.
	 * @param		array		$_cfg		Memcache configuration parameters
	 * @see		interface ICache.start
	 */
	public function __construct( &$_cfg ) {
		$Mem = new Memcache;
		foreach ( $_cfg as $_val ) {
			call_user_func_array(array($Mem, 'addServer'), $_val);
		}
		self::$Mem = $Mem;
		$_status = $Mem->getstats();
		if ( empty($_status) ) die("Unable to connect the Memcache server!");
	}
	
	/**
	 * get cache content from cache file or memcache.
	 *
	 * @see		interface ICache::get()
	 */
	public function get( $_key, $_ctime, $_serial, $_ctype=NULL ) {
		$_res = self::$Mem->get($_key);
		if ( $_res != FALSE ) Debug::appendMessage("加载Memcache数据成功！");
		$this->_key = $_key;
		if ( $_ctime != NULL ) $this->_ctime = $_ctime;
		return $_res;
	}
	
	/**
	 * set or updata cache
     * 
	 * @see		interface ICache::set()
	 */
	public function set( $_key, $_content, $_serial, $_ctype=NULL) {
		$_res = self::$Mem->set($this->_key, $_content, MEMCACHE_COMPRESSED, $this->_ctime);
		if ( $_res ) Debug::appendMessage("添加数据到MemCache成功！");
		return $_res;
	}

	/**
 	 * delete a element from memcache server(从服务端删除一个元素)
     * 
	 * @see				interface ICache::delete()
	 */
	public function delete( $_key, $_serial, $_ctype=NULL ) {
		return self::$Mem->delete($_key, 0);
	}
}
?>
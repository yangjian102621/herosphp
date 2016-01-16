<?php
/*---------------------------------------------------------------------
 * 缓存类统一接口，所有的缓存类必须实现这一接口。
 * cache operation class common interface.
 * 缓存的分类：对于文件缓存，其格式是这样的
 * 1. 有baseKey：baseKey/fname/factor/filename
 * 如：article/list/100/article-list-100.html
 * article/detail/90/article-detail-190.html
 * 2. 有key : common/{hash($key)}/{$key}.html
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\cache\interfaces;

interface ICache {
	
	/**
	 * 获取缓存内容
	 * 
	 * @param string $key 缓存的key值,如果设置为null则自动生成key
	 * @param string $expire  缓存有效期,如果等于0表示永不过期
     * @return mixed
	 */
	public function get( $key, $expire=null );
	
	/**
	 * 添加|更新缓存
	 * @param   string $key 缓存的key值, 如果设置为null则自动生成key
     * @param   string $content 缓存内容
     * @param string $expire  缓存有效期,如果等于0表示永不过期,只对memcaceh缓存有效
     * @param   boolean
	 */
	public function set( $key, $content, $expire=null );
	
	/**
	 * 删除缓存 
	 * @param string $key 缓存的key值。
     * @return boolean
	 */
	public function delete( $key );
		
}
?>
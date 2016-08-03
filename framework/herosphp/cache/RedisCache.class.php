<?php
/*---------------------------------------------------------------------
 * Redis 缓存
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\cache;

use herosphp\cache\interfaces\ICache;
use herosphp\utils\RedisUtils;

class RedisCache implements ICache {

    //所有缓存key的前缀
    const KEY_PREFIX = "CACHE_KET_PRIFIX_";

    /**
     * @var array 配置信息
     */
    private $configs = array();

    /**
     * 初始化缓存配置信息
     * @param array $configs 缓存配置信息
     */
    public function __construct( $configs ) {
        $this->configs = $configs;
    }

    /**
     * @see    ICache::get()
     * @param string $key
     * @return array|mixed|string
     */
	public function get($key, $expire=null) {
		return RedisUtils::getInstance()->get(self::KEY_PREFIX.$key);
	}

    /**
     * @see ICache::set()
     * @param string $key
     * @param string $content
     * @param null $expire
     * @return bool
     */
	public function set($key, $content, $expire=0) {

		return RedisUtils::getInstance()->set(self::KEY_PREFIX.$key, $content, $expire);
	}

    /**
     * @see    ICache::delete()
     * @param string $key
     * @return bool
     */
	public function delete( $key ) {
		return RedisUtils::getInstance()->delete(self::KEY_PREFIX.$key);
	}
}
?>
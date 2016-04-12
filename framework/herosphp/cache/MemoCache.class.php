<?php
/*---------------------------------------------------------------------
 * memcache 缓存
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\cache;

use herosphp\cache\interfaces\ICache;

class MemoCache implements ICache {

    /**
     * Memcache 缓存实例
     * @var Memcache|null
     */
    private static $Mem = NULL;

    /**
     * @var array 配置信息
     */
    private $configs = array();

    /**
     * 初始化缓存配置信息
     * @param array $configs 缓存配置信息
     */
    public function __construct( $configs ) {
        if ( !$configs ) {
            if ( APP_DEBUG ) {
                E("必须传入缓存配置信息！");
            }
        }
        $this->configs = $configs;
        $Mem = new \Memcache();
        foreach ( $this->configs['server'] as $value ) {
            call_user_func_array(array($Mem, 'addServer'), $value);
        }
        if ( !$Mem->getstats() ) {
            if ( APP_DEBUG ) {
                E("Unable to connect the Memcache server!");
            }
        }
        self::$Mem = $Mem;
    }

    /**
     * @see    ICache::get()
     * @param string $key
     * @param null $expire
     * @return array|mixed|string
     */
	public function get( $key, $expire=null ) {
		return self::$Mem->get($key);
	}

    /**
     * @see ICache::set()
     * @param string $key
     * @param string $content
     * @param null $expire
     * @return bool
     */
	public function set( $key, $content, $expire=null) {

        if ( $expire !== null ) {
            $this->configs['expire'] = $expire;
        }
		return self::$Mem->set($key, $content, MEMCACHE_COMPRESSED, $this->configs['expire']);
	}

    /**
     * @see    ICache::delete()
     * @param string $key
     * @return bool
     */
	public function delete( $key ) {
		return self::$Mem->delete($key, 0);
	}
}
?>

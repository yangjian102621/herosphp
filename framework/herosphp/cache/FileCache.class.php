<?php
/*---------------------------------------------------------------------
 * 动态文件缓存, 可用于缓存数据库的查询结果, 或者是对页面的局部缓存, 实现ICache接口
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\cache;

use herosphp\cache\interfaces\ICache;
use herosphp\core\Debug;
use herosphp\utils\ArrayUtils;
use herosphp\utils\FileUtils;

class FileCache extends ACache implements ICache {

    /**
     * @see        ICache::get()
     * @param string $key
     * @param null $expire
     * @return bool|mixed|string
     */
	public function get( $key, $expire=null ) {

        if ( $expire !== null ) {
            $this->configs['expire'] = $expire;
        }
	    $cacheFile = $this->getCacheFile($key);

        //缓存文件不存在
		if ( !file_exists($cacheFile) ) {
            if ( APP_DEBUG ) {
                Debug::appendMessage("缓存文件 {$cacheFile} 不存在.");
            }
			return false;
		}
		//缓存过期, 若$expire = 0 则表示缓存永不过期
		if ( $this->configs['expire'] > 0 &&
            time() > (filemtime($cacheFile) + $this->configs['expire']) ) {

            if ( APP_DEBUG ) {
                Debug::appendMessage("缓存文件 {$cacheFile} 已经过期.");
            }
			return false;
		} else {
			$content = file_get_contents($cacheFile);
            if ( ArrayUtils::isSerializedArray($content) ) {
                return unserialize($content);
            } else {
                return $content;
            }
		}
	}


    /**
     * @see   ICache::set();
     * @param string $key
     * @param string $content
     * @param null $expire
     * @return int
     */
	public function set( $key, $content, $expire=null ) {

        $cacheFile = $this->getCacheFile($key);
        $dirname = dirname($cacheFile);
        if ( !file_exists($dirname) ) {
            FileUtils::makeFileDirs($dirname);
        }
        if ( is_array($content) ) $content = serialize($content);
		return file_put_contents($cacheFile, $content, LOCK_EX);
	}

    /**
     * @see        ICache::delete()
     * @param string $key
     * @return bool
     */
	public function delete( $key ) {
        $cacheFile = $this->getCacheFile($key);
		return @unlink($cacheFile);
	}

}
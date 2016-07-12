<?php
/*---------------------------------------------------------------------
 * 动态文件缓存, 可用于缓存数据库的查询结果, 或者是对页面的局部缓存, 实现ICache接口
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
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
	public function get( $key ) {

	    $cacheFile = $this->getCacheFile($key);

        //缓存文件不存在
		if ( !file_exists($cacheFile) ) return false;

        $text = file_get_contents($cacheFile);
        $content = cn_json_decode($text);
		//判断缓存是否过期
		if ( $content['expire'] > 0 && time() > (filemtime($cacheFile) + $content['expire']) ) {
			return false;
		} else {
            return $content['data'];
		}
	}


    /**
     * @see   ICache::set();
     * @param string $key
     * @param string $content
     * @param null $expire
     * @return int
     */
	public function set( $key, $content, $expire=0 ) {

        $cacheFile = $this->getCacheFile($key);
        $dirname = dirname($cacheFile);
        if ( !file_exists($dirname) ) {
            FileUtils::makeFileDirs($dirname);
        }
        $data['expire'] = $expire;
        $data['data'] = $content;
		return file_put_contents($cacheFile, cn_json_encode($data), LOCK_EX);
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
<?php
/*---------------------------------------------------------------------
 * HTML缓存生成类, 实现ICache接口
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
use herosphp\utils\FileUtils;

class HtmlCache extends ACache implements ICache {

    /**
     * 缓存文件后缀
     * @var string
     */
    private $cacheExt = '.html';


    /**
     * @see        ICache::get()
     */
    public function get( $key, $expire = null ) {

        if ( $expire ) $this->configs['expire'] = $expire;
        $cacheFile = $this->getCacheFile($key, $this->cacheExt);

        //缓存文件不存在
        if ( !file_exists($cacheFile) ) {
            Debug::appendMessage("缓存文件 {$cacheFile} 不存在.");
            return false;
        }
        //缓存过期, 若ctime = -1 则表示缓存永不过期
        if ( $this->configs['expire'] >= 0 &&
            time() > (filemtime($cacheFile) + $this->configs['expire']) ) {

            Debug::appendMessage("缓存文件 {$cacheFile} 已经过期.");
            return false;
        } else {
            return file_get_contents($cacheFile);
        }
    }

    /**
     * @see   ICache::set();
     */
    public function set( $key, $content ) {

        $cacheFile = $this->getCacheFile($key, $this->cacheExt);
        $dirname = dirname($cacheFile);
        if ( !file_exists($dirname) ) {
            FileUtils::makeFileDirs($dirname);
        }
        return file_put_contents($cacheFile, $content, LOCK_EX);
    }

    /**
     * @see		ICache::delete()
     */
    public function delete( $key ) {
        $cacheFile = $this->getCacheFile($key, $this->cacheExt);
        return @unlink($cacheFile);
    }
}
?>
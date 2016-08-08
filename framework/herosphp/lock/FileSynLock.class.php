<?php

namespace herosphp\lock;

/*---------------------------------------------------------------------
 * 同步锁，通过文件锁实现
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

use herosphp\core\Loader;
use herosphp\files\FileUtils;
use herosphp\lock\interfaces\ISynLock;

Loader::import('lock.interfaces.ISynLock', IMPORT_FRAME);

class FileSynLock implements ISynLock {

    private $file_handler = false;  //文件资源柄

    public function __construct($key)
    {
        $lockDir = APP_RUNTIME_PATH.'lock/';
        FileUtils::makeFileDirs($lockDir);
        $this->file_handler = fopen($lockDir.md5($key).'.lock', 'w');
    }

    /**
     * 尝试去获取锁，成功返回false并且一直阻塞
     * @throws \herosphp\exception\HeroException
     */
    public function tryLock()
    {
        if ( flock($this->file_handler, LOCK_EX) === false ) {
            E('获取文件锁失败，锁定失败.');
        }
    }

    /**
     * 释放锁
     * @throws \herosphp\exception\HeroException
     */
    public function unlock()
    {
        if ( flock($this->file_handler, LOCK_UN) === false ) {
            E('释放文件锁失败.');
        }
    }

    public function __destruct()
    {
        if ( $this->file_handler !== false ) {
            fclose($this->file_handler);
        }
    }
}
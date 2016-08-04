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
     * @return mixed
     */
    public function tryLock()
    {
        return flock($this->file_handler, LOCK_EX);
    }

    /**
     * 释放锁
     * @return mixed
     */
    public function unlock()
    {
        return flock($this->file_handler, LOCK_UN);
    }

    public function __destruct()
    {
        if ( $this->file_handler !== false ) {
            fclose($this->file_handler);
        }
    }
}
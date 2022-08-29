<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\utils;

/**
 * file lock implements
 * ------------------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class Lock
{

    private mixed $_handler;

    private function __construct($key,)
    {
        $lock_dir = RUNTIME_PATH . 'lock/';
        if (!file_exists($lock_dir)) {
            FileUtil::makeFileDirs($lock_dir);
        }

        $this->_handler = fopen($lock_dir . md5($key) . 'lock', 'w');
    }

    public static function get(string $key): Lock
    {
        $lock = new static($key);
        return $lock;
    }

    public function tryLock(): bool
    {
        return flock($this->_handler, LOCK_EX);
    }

    public function unlock()
    {
        return flock($this->_handler, LOCK_UN);
    }

    public function __destruct()
    {
        if ($this->_handler) {
            fclose($this->_handler);
        }
    }
}

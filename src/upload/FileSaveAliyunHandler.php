<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\upload;

use herosphp\exception\HeroException;

/**
 * 阿里云 OSS 文件上传 Handler 实现
 * --------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class FileSaveAliyunHandler implements IFileSaveHandler
{
    protected array $_config = [];

    public function __construct($config = null)
    {
        if ($config !== null) {
            $this->_config = $config;
        }
    }

    public function save(string $srcFile, string $filename): string|false
    {
        throw new HeroException('Not implemented.');
    }

    public function saveBase64($data, string $filename): string|false
    {
        throw new HeroException('Not implemented.');
    }
}

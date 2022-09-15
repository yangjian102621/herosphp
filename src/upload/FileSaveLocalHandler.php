<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\upload;

use herosphp\utils\FileUtil;

/**
 * 本地文件上传 Handler 实现
 * --------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class FileSaveLocalHandler implements IFileSaveHandler
{
    protected array $_config = [];

    public function __construct($config = null)
    {
        if ($config !== null) {
            $this->_config = $config;
        }

        // create upload dir
        if (!file_exists($this->_config['upload_dir'])) {
            FileUtil::makeFileDirs($this->_config['upload_dir']);
        }
    }

    public function save(string $srcFile, string $filename): string|false
    {
        $dstFile = $this->_config['upload_dir'] . DIRECTORY_SEPARATOR . $filename;
        // 此处不能用 move_uploaded_file() 函数， 不支持 Workerman 的上传协议
        if (file_exists($srcFile) && rename($srcFile, $dstFile)) {
            return $dstFile;
        }

        return false;
    }

    public function saveBase64($data, string $filename): string|false
    {
        $image = base64_decode($data);
        if (!$image) {
            return false;
        }

        $dstFile = $this->_config['upload_dir'] . DIRECTORY_SEPARATOR . $filename;
        if (file_put_contents($dstFile, $data)) {
            return $dstFile;
        }

        return false;
    }
}

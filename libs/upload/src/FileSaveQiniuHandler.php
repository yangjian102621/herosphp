<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace HerosUpload;

use herosphp\exception\HeroException;
use herosphp\upload\IFileSaveHandler;
use herosphp\utils\Logger;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

/**
 * 七牛云 OSS 文件上传 Handler 实现
 * --------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class FileSaveQiniuHandler implements IFileSaveHandler
{
    protected static array $_config = [];

    protected static mixed $_token;

    public function __construct($config = null)
    {
        if ($config !== null) {
            static::$_config = $config;
        }

        // create Authentication
        $auth = new Auth(static::$_config['access_key'], static::$_config['secret_key']);
        static::$_token = $auth->uploadToken(static::$_config['bucket']);
    }

    public function save(string $srcFile, string $filename): string|false
    {
        $uploadMgr = new UploadManager();
        [$ret, $err] = $uploadMgr->putFile(static::$_token, $filename, $srcFile, null, 'application/octet-stream', true, null, 'v2');
        if ($err !== null) {
            Logger::warn($err);
        } else {
            return static::$_config['domain'] . $ret['key'];
        }
    }

    public function saveBase64($data, string $filename): string|false
    {
        throw new HeroException('Not implemented.');
    }
}

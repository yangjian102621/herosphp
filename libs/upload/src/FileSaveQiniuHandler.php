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
use herosphp\utils\StringUtil;
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

    public function __construct(array $config)
    {
        if (
            empty($config) ||
            !isset($config['access_key']) ||
            !isset($config['secret_key']) ||
            !isset($config['bucket'])
        ) {
            throw new HeroException('Invalid upload configs');
        }

        // create Authentication
        $auth = new Auth($config['access_key'], $config['secret_key']);
        static::$_token = $auth->uploadToken($config['bucket']);

        if (!str_ends_with($config['domain'], '/')) {
            $config['domain'] = $config['domain'] . '/';
        }

        static::$_config = $config;
    }

    public function save(string $srcFile, string $filename): string|false
    {
        $uploadMgr = new UploadManager();
        [$ret, $err] = $uploadMgr->putFile(static::$_token, $filename, $srcFile, null, 'application/octet-stream', true, null, 'v2');
        if ($err !== null) {
            Logger::warn($err);
            return false;
        }
        return static::$_config['domain'] . $ret['key'];
    }

    public function saveBase64($data, string $filename): string|false
    {
        // 上传文件服务地址
        // 华东空间：upload.qiniu.com
        // 华北空间: upload-z1.qiniu.com
        // 华南空间: upload-z2.qiniu.com
        // 北美空间: upload-na0.qiniu.com

        $url = 'http://upload-z2.qiniu.com/putb64/-1/key/' . base64_encode($filename);
        $headers = [
            'Content-Type:image/png',
            'Authorization:UpToken ' . static::$_token,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $ret = curl_exec($ch);
        curl_close($ch);

        $ret = StringUtil::jsonDecode($ret);
        if (isset($ret['error']) && $ret['error'] !== null) {
            Logger::warn($ret['error']);
            return false;
        }
        return static::$_config['domain'] . $ret['key'];
    }
}

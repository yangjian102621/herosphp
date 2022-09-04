<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\upload;

/**
 * 文件上传错误信息列表
 * ---------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
enum UploadError: int
{
    case SUCCESS = 0;
    case FILESIZE_OVER_LIMIT = 1;
    case EXT_NOT_ALLOW = 2;
    case SAVE_FILE_FAIL = 3;
    case IMG_DECODE_FAIL = 4;
    case PART_UPLOADED = 5;

    public function getName(): string
    {
        return match ($this) {
            static::SUCCESS => '文件上传成功',
            static::FILESIZE_OVER_LIMIT => '文件超出大小限制',
            static::EXT_NOT_ALLOW => '非法的文件后缀名',
            static::SAVE_FILE_FAIL => '保存上传文件失败',
            static::IMG_DECODE_FAIL => 'Base64 图片解码失败',
            static::PART_UPLOADED => '部分文件上传成功',
        };
    }
}

<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\upload;

/**
 * 文件上传返回信息 VO
 * ---------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class UploadFileInfo
{
    // local name
    public string $localName;

    // new  filename for uploaded file
    public string $name;

    // absolute path
    public string $path;

    // filesize bytes
    public int $fileSize;

    // file extension
    public string $extension;

    // file mine type
    public string $mimeType;

    // mark for image
    public bool $isImage = false;

    public function __construct(string $localName, int $fileSize, string $extension, string $mimeType)
    {
        $this->localName = $localName;
        $this->fileSize = $fileSize;
        $this->extension = $extension;
        $this->mimeType = $mimeType;
    }

    public function getImageSize(): array|bool
    {
        if ($this->isImage === false) {
            return false;
        }

        $size = getimagesize($this->path);
        if ($size === false) {
            return false;
        }

        return ['width' => $size[0], 'height' => $size[1]];
    }
}

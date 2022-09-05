<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

namespace herosphp\upload;

use herosphp\utils\FileUtil;

/**
 * 文件上传类, 支持多文件上传不重名覆盖。支持base64编码文件上传
 * ---------------------------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class UploadFile
{
    // Upload configuration
    protected array $_config = [
        // File save handler
        'save_handler' => FileSaveLocalHandler::class,
        'handler_config' => ['upload_dir' => RUNTIME_PATH . 'upload'],
        // Allowed file extension
        'allow_ext' => 'jpg|jpeg|png|gif|txt|pdf|rar|zip|swf|bmp|c|java|mp3',
        // Allowed max file size, default value is  5MiB,
        // if no limits, set it to 0
        'max_size' => 5242880
    ];

    /**
     * File info of the uploaded file
     * @see UploadFileInfo
     */
    protected array $_fileInfos = [];

    // Upload error code
    protected UploadError $_errorNo = UploadError::SUCCESS;

    // File save handler
    protected ?IFileSaveHandler $_handler = null;

    // Constructor
    public function __construct(array $config = null)
    {
        if ($config !== null) {
            $this->_config = array_merge($this->_config, $config);
        }
        ini_set('upload_max_filesize', ceil($this->_config['max_size'] / 1048576) . 'M');

        // init file save handler
        if ($this->_config['handler_config'] === null) {
            $this->_handler = new $this->_config['save_handler']();
        } else {
            $this->_handler = new $this->_config['save_handler']($this->_config['handler_config']);
        }
    }

    // Upload file
    public function upload(array $files = null): array|bool|UploadFileInfo
    {
        if (empty($files)) {
            return false;
        }

        if ($this->_isMultiple($files)) {
            foreach ($files as $file) {
                $filename = $this->_doUpload($file);
                if ($filename !== false) {
                    $this->_fileInfos[] = $filename;
                }
            }
        } else {
            return $this->_doUpload($files);
        }

        if (empty($this->_fileInfos)) {
            return false;
        }

        if (count($this->_fileInfos) !== count($files)) {
            $this->_errorNo = UploadError::PART_UPLOADED;
        }

        return $this->_fileInfos;
    }

    // get upload error code
    public function getUploadErrNo()
    {
        return $this->_errorNo;
    }

    // upload base64 image data
    public function uploadBase64($data): UploadFileInfo|bool
    {
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $data, $match)) {
            $data = str_replace($match[1], '', $data);
        }

        $image = base64_decode($data);
        if (!$image) {
            $this->_errorNo = UploadError::IMG_DECODE_FAIL;
            return false;
        }

        $filename = static::_genFilename('png');
        $fileInfo = new UploadFileInfo($filename, strlen($image), 'png', 'image/png');
        $fileInfo->name = $filename;

        $path = $this->_handler->saveBase64($image, $fileInfo->name);
        if ($path === false) {
            $this->_errorNo = UploadError::SAVE_FILE_FAIL;
            return false;
        }

        if (str_starts_with($fileInfo->mimeType, 'image')) {
            $fileInfo->isImage = true;
        }

        $fileInfo->path = $path;

        return $fileInfo;
    }

    // do upload single file
    protected function _doUpload(array $file): UploadFileInfo|bool
    {
        $ext = FileUtil::getFileExtension($file['name']);
        $check = $this->_checkFileExtension($ext) && $this->_checkFileSize($file['tmp_name']);
        if ($check === false) {
            return false;
        }

        $fileInfo = new UploadFileInfo($file['name'], $file['size'], $ext, $file['type']);
        $fileInfo->name = static::_genFilename($ext);
        if (str_starts_with($fileInfo->mimeType, 'image')) {
            $fileInfo->isImage = true;
        }

        $path = $this->_handler->save($file['tmp_name'], $fileInfo->name);
        if ($path === false) {
            $this->_errorNo = UploadError::SAVE_FILE_FAIL;
            return false;
        }

        $fileInfo->path = $path;

        return $fileInfo;
    }

    // generate a filename
    protected static function _genFilename(string $ext): string
    {
        return bin2hex(pack('d', microtime(true)) . random_bytes(8)) . ".{$ext}";
    }

    // check if is multiple upload
    protected function _isMultiple(array $files): bool
    {
        $value = array_values($files);
        $diff = array_diff_key($files, $value);
        return empty($diff);
    }

    // check file extension
    protected function _checkFileExtension(string $ext): bool
    {
        if ($this->_config['allow_ext'] === '*') {
            return true;
        }

        $ext = strtolower($ext);
        $allowedExt = explode('|', $this->_config['allow_ext']);
        if (!in_array($ext, $allowedExt)) {
            $this->_errorNo = UploadError::EXT_NOT_ALLOW;
            return false;
        }
        return true;
    }

    // check file size
    protected function _checkFileSize($path): bool
    {
        if (filesize($path) > $this->_config['max_size']) {
            $this->_errorNo = UploadError::FILESIZE_OVER_LIMIT;
            return false;
        }

        return true;
    }
}

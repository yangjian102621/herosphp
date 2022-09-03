<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

namespace herosphp\upload;

/**
 * 文件上传类, 支持多文件上传不重名覆盖。支持base64编码文件上传
 * ---------------------------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class UploadFile
{
    // 配置参数
    protected array $_config = [
        //上传文件的根目录
        'upload_dir' => RUNTIME_PATH . 'upload/',
        //允许上传的文件类型
        'allow_ext' => 'jpg|jpeg|png|gif|txt|pdf|rar|zip|swf|bmp|c|java|mp3',
        //图片的最大宽度, 0 代表不限制
        'max_width' => 0,
        //图片的最大高度, 0 代表不限制
        'max_height' => 0,
        //文件的最大尺寸，单位 KiB，0 代表不限制大小
        'max_size' => 1024,
    ];

    // 返回文件信息，如果上传了多个文件则返回一个 UploadFileInfo 数组
    protected array|UploadFileInfo $_fileInfo = [];

    // 上传错误代码
    protected UploadError $_errorNo = UploadError::SUCCESS;

    protected string $_handlerClass = FileSaveLocalHandler::class;
    protected ?IFileSaveHandler $_handler = null;


    // Constructor
    public function __construct($config = null)
    {
        if ($config !== null) {
            $this->_config = array_merge($this->_config, $config);
        }
        ini_set('upload_max_filesize', ceil($this->config['max_size'] / 1024) . 'M');
    }

    /**
     * upload file method.
     * @param        sting $_field name of form elements.
     * @param        bool $_base64
     * @return       mixed false or file info array.
     */
    public function upload($_field, $_base64 = false)
    {
        if (!$this->checkUploadDir()) {
            $this->errNum = 6;
            return false;
        }

        if ($_base64) {
            $_data = $_POST[$_field];
            return $this->makeBase64Image($_data);
        }

        $_localFile = $_FILES[$_field]['name'];
        if (!$_localFile) {
            $this->errNum = 10;
            return false;
        }
        $_tempFile = $_FILES[$_field]['tmp_name']; //原来是这样
        //$_tempFile = str_replace('\\\\', '\\', $_FILES[$_field]['tmp_name']);//MAGIC_QUOTES_GPC=OFF时，做了这样处理：$_FILES = daddslashes($_FILES);图片上传后tmp_name值变成 X:\\Temp\\php668E.tmp，结果move_uploaded_file() 函数判断为不合法的文件而返回FALSE。
        $_error_no = $_FILES[$_field]['error'];
        $this->fileInfo['file_type'] = $_FILES[$_field]['type'];
        $this->fileInfo['local_name'] = $_localFile;
        $this->fileInfo['file_size'] = $_FILES[$_field]['size'];

        $this->errNum = $_error_no;
        if ($this->errNum == 0) {
            $this->checkFileType($_localFile);
            if ($this->errNum == 0) {
                $this->checkFileSize($_tempFile);
                if ($this->errNum == 0) {
                    if (is_uploaded_file($_tempFile)) {
                        $_new_filename = $this->getFileName($_localFile);
                        $this->fileInfo['file_path'] = $this->config['upload_dir'] . DIRECTORY_SEPARATOR . $_new_filename;
                        if (move_uploaded_file($_tempFile, $this->fileInfo['file_path'])) {
                            $_filename = $_new_filename;
                            $this->fileInfo['file_name'] = $_filename;
                            $pathinfo = pathinfo($this->fileInfo['file_path']);
                            $this->fileInfo['file_ext'] = $pathinfo['extension'];
                            $this->fileInfo['raw_name'] = $pathinfo['filename'];

                            return $this->fileInfo;
                        }
                        $this->errNum = 7;
                    }
                }
            }
        }

        return false;
    }

    /**
     * 获取新的文件名
     * @param $filename
     * @return string
     */
    public function getFileName($filename)
    {
        $_ext = $this->getFileExt($filename);
        return time() . '-' . mt_rand(100000, 999999) . '.' . $_ext;
    }

    /**
     * 创建多级目录
     * @param $path
     * @return bool
     */
    public static function makeFileDirs($path)
    {
        //必须考虑 "/" 和 "\" 两种目录分隔符
        $files = preg_split('/[\/|\\\]/s', $path);
        $_dir = '';
        foreach ($files as $value) {
            $_dir .= $value . DIRECTORY_SEPARATOR;
            if (!file_exists($_dir)) {
                mkdir($_dir);
            }
        }
        return true;
    }

    /**
     * get upload message
     * @return       string
     */
    public function getUploadMessage()
    {
        if ($this->errNum == 9) {
            return "尺寸超出{$this->config['max_width']}x{$this->config['max_height']}";
        }
    }

    /**
     * 接收base64位参数，转存图片
     * @param $_base64_data
     * @return bool|string
     */
    protected function makeBase64Image($_base64_data)
    {
        $_img = base64_decode($_base64_data);
        $this->fileInfo['local_name'] = time() . '.png';
        $_filename = $this->getFileName($this->fileInfo['local_name']);
        $this->fileInfo['file_name'] = $_filename;
        $this->fileInfo['file_path'] = $this->config['upload_dir'] . DIRECTORY_SEPARATOR . $_filename;
        if (file_put_contents($this->fileInfo['file_path'], $_img) && file_exists($this->fileInfo['file_path'])) {
            $size = getimagesize($this->fileInfo['file_path']);
            if (($this->config['max_width'] > 0 && $size[0] > $this->config['max_width'])
                || ($this->config['max_height'] > 0 && $size[1] > $this->config['max_height'])
            ) {
                $this->errNum = 9;
                return false;
            }
            $this->fileInfo['image_width'] = $size[0];
            $this->fileInfo['image_height'] = $size[1];
            //初始化mimeType
            $this->fileInfo['file_type'] = 'image/png';
            $this->fileInfo['is_image'] = 1;
            $this->fileInfo['file_size'] = filesize($this->fileInfo['file_path']);

            $pathinfo = pathinfo($this->fileInfo['file_path']);
            $this->fileInfo['file_ext'] = $pathinfo['extension'];
            $this->fileInfo['raw_name'] = $pathinfo['filename'];

            return $this->fileInfo;
        }
        $this->errNum = 8;

        return false;
    }

    /**
     * 检测上传目录
     * @return bool
     */
    protected function checkUploadDir()
    {
        if (!file_exists($this->config['upload_dir'])) {
            return self::makeFileDirs($this->config['upload_dir']);
        }
        return true;
    }

    /**
     * 检测文件类型是否合法
     * @param $filename
     * @return boolean
     */
    protected function checkFileType($filename)
    {
        if ($this->config['allow_ext'] == '*') {
            return true;
        }
        $_ext = strtolower(self::getFileExt($filename));
        $_allow_ext = explode('|', $this->config['allow_ext']);
        if (!in_array($_ext, $_allow_ext)) {
            $this->errNum = 5;
            return false;
        }
        return true;
    }

    /**
     * 获取文件名后缀
     * @param $filename
     * @return string
     */
    protected function getFileExt($filename)
    {
        $_pos = strrpos($filename, '.');
        return strtolower(substr($filename, $_pos + 1));
    }

    /**
     * 检查文件大小是否合格
     * @param $filename
     */
    protected function checkFileSize($filename)
    {
        if (filesize($filename) > $this->config['max_size']) {
            $this->errNum = 1;
        }

        //如果是图片还要检查图片的宽度和高度是否超标
        $size = getimagesize($filename);
        if ($size != false) {
            $this->fileInfo['is_image'] = 1;
            if (($this->config['max_width'] > 0 && $size[0] > $this->config['max_width'])
                || ($this->config['max_height'] > 0 && $size[1] > $this->config['max_height'])
            ) {
                $this->errNum = 9;
            }
            $this->fileInfo['image_width'] = $size[0];
            $this->fileInfo['image_height'] = $size[1];
        } else {
            $this->fileInfo['is_image'] = 0;
        }
    }
}

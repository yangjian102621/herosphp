<?php
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\utils;

/**
 * File utils
 * ---------------------------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class FileUtils
{

    public static function makeFileDirs($path): bool
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

        return file_exists($path);
    }

    /**
     * 获取文件后缀名称
     * @param string  	文件名
     * @return string
     */
    public static function getFileExtension($filename): string
    {
        $_pos = strrpos($filename, '.');
        return strtolower(substr($filename, $_pos + 1));
    }

    /**
     * 递归删除文件夹
     * @param $dir
     * @return boolean
     */
    public static function removeDirs($dir): bool
    {

        $handle = opendir($dir);
        //删除文件夹下面的文件
        while ($file = readdir($handle)) {
            if ($file != "." && $file != "..") {
                $filename = $dir . "/" . $file;
                if (!is_dir($filename)) {
                    @unlink($filename);
                } else {
                    static::removeDirs($filename);
                }
            }
        }
        closedir($handle);

        return rmdir($dir);
    }

    /**
     * 拷贝目录
     * @param $src 源文件
     * @param $dst 目标文件
     * @return boolean
     */
    public static function copyDir($src, $dst)
    {
        //如果是文件，则直接拷贝
        if (is_file($src)) {
            return copy($src, $dst);
        }

        @mkdir($dst);   //创建目标目录
        $handle = opendir($src);
        if ($handle !== false) {
            while (($filename = readdir($handle))) {

                if ($filename == '.'  || $filename == '..') continue;
                $fileSrc = $src . '/' . $filename;
                $fileDst = $dst . '/' . $filename;
                if (is_dir($fileSrc)) {
                    self::copyDir($fileSrc, $fileDst);
                } else {
                    copy($fileSrc, $fileDst);
                }
            }
        }
        closedir($src);
    }

    /**
     * 判断一个目录是否为空
     * @param $dirName
     * @return boolean
     */
    public static function isEmptyDir($dirName)
    {
        $handle = opendir($dirName);
        if ($handle != FALSE) {
            while (($filename = readdir($handle)) != false) {
                if ($filename != '.' && $filename != '..')
                    return false;
            }
        }
        closedir($handle);
        return true;
    }
}

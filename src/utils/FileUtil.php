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
class FileUtil
{
    public static function makeFileDirs($path): bool
    {
        // Both / and \ dir separators should be considered
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

    // get file extension
    public static function getFileExtension($filename): string
    {
        return strtolower(substr(strrchr($filename, '.'), 1));
    }

    // delete dirs recursively
    public static function removeDirs($dir): bool
    {
        $handle = opendir($dir);
        while ($file = readdir($handle)) {
            if ($file != '.' && $file != '..') {
                $filename = $dir . '/' . $file;
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

    // copy a file or a dir
    public static function copyDir($src, $dst): bool
    {
        // single file
        if (is_file($src)) {
            return copy($src, $dst);
        }

        static::makeFileDirs($dst);
        $handle = opendir($src);
        if ($handle !== false) {
            while (($filename = readdir($handle))) {
                if ($filename == '.' || $filename == '..') {
                    continue;
                }
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
        return true;
    }

    // check if a dir is empty
    public static function isEmptyDir($dirName): bool
    {
        $handle = opendir($dirName);
        if ($handle != false) {
            while (($filename = readdir($handle)) != false) {
                if ($filename != '.' && $filename != '..') {
                    return false;
                }
            }
        }
        closedir($handle);
        return true;
    }
}

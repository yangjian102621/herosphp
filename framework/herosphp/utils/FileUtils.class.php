<?php

namespace herosphp\utils;

/*---------------------------------------------------------------------
 * HerosPHP 文件操作工具类
 * @package herosphp\utils
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

class FileUtils {

	/**
	 * 创建多层文件目录
	 * @param 	string 		$path			需要创建路径
	 * @return 	boolean     成功时返回true，失败则返回false;
	 */
	public static function makeFileDirs( $path ) {
        //必须考虑 "/" 和 "\" 两种目录分隔符
        $files = preg_split('/[\/|\\\]/s', $path);
        $_dir = '';
        foreach ($files as $value) {
            $_dir .= $value.DIRECTORY_SEPARATOR;
            if ( !file_exists($_dir) ) {
                mkdir($_dir);
            }
        }
        return true;
	}

	/**
     * 获取文件后缀名称
     * @param string  	文件名
     * @return string
	 */
	public static function getFileExt( $filename ) {
		$_pos = strrpos( $filename, '.' );
		return strtolower( substr( $filename , $_pos+1 ) );
	}

    /**
     * 递归删除文件夹
     * @param $dir
     * @return boolean
     */
    public static function removeDirs( $dir ) {

        $handle = opendir($dir);
        //删除文件夹下面的文件
        while ( $file=readdir($handle) ) {
            if( $file != "." && $file != ".." ) {
                $filename = $dir."/".$file;
                if( !is_dir($filename) ) {
                    @unlink($filename);
                } else {
                    self::removeDirs($filename);
                }
            }
        }
        closedir($handle);

        //删除当前文件夹
        if( rmdir($dir) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 拷贝目录
     * @param $src 源文件
     * @param $dst 目标文件
     * @return boolean
     */
    public static function copyDir( $src, $dst ) {
        if ( is_file($src) ) {  //如果是文件，则直接拷贝
            return copy($src, $dst);
        }
        @mkdir($dst);   //创建目标目录
        $handle = opendir($src);
        if ( $handle !== false ) {
            while( ($filename = readdir($handle)) ) {

                if ( $filename == '.'  || $filename == '..' ) continue;
                $fileSrc = $src.'/'.$filename;
                $fileDst = $dst.'/'.$filename;
                if ( is_dir($fileSrc) ) {
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
    public static function isEmptyDir($dirName) {
        $handle = opendir($dirName);
        if ( $handle != FALSE ) {
            while ( ($filename = readdir($handle)) != false  ) {
                if ( $filename != '.' && $filename != '..' )
                    return false;
            }
        }
        closedir($handle);
        return true;
    }
}
?>

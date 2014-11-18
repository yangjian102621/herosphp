<?php
/*---------------------------------------------------------------------
 * HerosPHP 文件操作工具类
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\utils;

class FileUtils {

	/**
	 * 创建多层文件目录
	 * @param 	string 		$path			需要创建路径
	 * @return 	boolean     成功时返回true，失败则返回false;
	 */
	public static function makeFileDirs( $path ) {
		//必须考虑 "/" 和 "\" 两种目录分隔符
		$files = preg_split('/\/|\\\\/', $path);
		$dir = '';
		foreach ( $files as $value ) {
            $dir .= $value.DIRECTORY_SEPARATOR;
            if ( !is_readable($dir) ) continue;     //如果目录不可读则跳过

			if ( !file_exists($dir) ) {
				if ( !mkdir($dir) ) return false;
			}
		}
		return  true;
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
     */
    public static function removeDirs( $dir ) {

    }

    /**
     * 拷贝目录
     * @param $src 源文件
     * @param $dst 目标文件
     */
    public static function copyDir( $src, $dst ) {

    }
}
?>

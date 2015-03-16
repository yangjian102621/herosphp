<?php

namespace herosphp\utils;

/*---------------------------------------------------------------------
 * PHP 生成zip压缩文件类(pack and compress the point file or direcoty to zip file) <br />
 *  支持两种形式传入文件 <br/>
 * 	1.直接传入需要打包的文件的路径( String of path) <br />
 * 	2.通过表单浏览上传多个文件进行打包 ( Array of filename ) <br />
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

class PHPZip {
	
	/* 要压缩的文件路径 */
	private $file_path;
	
	/* 得到的压缩文件名称 */
	private $zip_name;
	
	/* ZipArchive类对象的一个引用 */
	private $zip;
	
	/* 各个文件夹在压缩包中的相对路径, hash数组 */
	private $zip_dir = array();
	
	/**
	 * constructor (构造函数)
	 */
	public function __construct() {
		//check the Apache server is surpport zip compress.
		if ( !@extension_loaded('zip') && !@function_exists("gzcompress") ) {
			E("当前服务器不支持压缩，请更改PHP的相关配置。");
		}
		@set_time_limit(0);

        //实例化压缩类
        $this->zip = new \ZipArchive();
	}

    /**
     * 创建zip压缩文件
     * @param $src
     * @param $zip
     * @return bool
     */
    public function createZip( $src, $zip ) {

		if ( !file_exists($src) ) E("压缩源文件不存在!");

        if ( !$zip ) E("压缩目标文件不能为空！");

        $this->file_path = $src;
        $this->zip_name = $zip;

		//创建压缩文件
		if ( !file_exists($this->zip_name) ) {
			if ( $this->zip->open($this->zip_name, \ZIPARCHIVE::CREATE) == FALSE ) {
				E("创建压缩文件失败");
			}
		} else {
			if ( $this->zip->open($this->zip_name, \ZIPARCHIVE::OVERWRITE) == FALSE ) {
				E("创建压缩文件失败");
			}	
		}
		
		if ( !is_array($this->file_path) ) {
			$this->addFilesToZip($this->file_path);
		} else {
			foreach ( $this->file_path as $_val ) {
				$this->addFilesToZip($_val);
			}	
		}
		$this->zip->close();
		if ( file_exists($this->zip_name) ) {
			return TRUE;
		} else {
			return FALSE;	
		}
	}

    /**
     * 解压文件到目标位置
     * @param $zip  zip文件
     * @param $dst  目标文件
     * @return boolean
     */
    public function extractZip($zip, $dst) {

        if ( $this->zip->open($zip) == TRUE ) {
           return $this->zip->extractTo($dst);
        }
        return false;
    }

    /**
     * method to add files to zip pack file. (添加文件到zip压缩包,如果是目录采用递归添加)
     * @param string $_files 需要打包的文件或者文件夹
     * @param string $_zip_dir
     */
	private function addFilesToZip( $_files, $_zip_dir = NULL ) {
		
		if ( is_dir($_files) ) {
			if ( ($handle = opendir($_files)) != FALSE ) {
				while ( ($filename = readdir($handle)) != FALSE ) {
					if ( $filename != '..' && $filename != '.' ) {
						if ( is_dir($_files.'/'.$filename) ) {
							//在压缩文件中新建目录
							$_new_dir = $_zip_dir == NULL ? $filename : $_zip_dir.'/'.$filename;
							/**
							 * 此处很重要，保存每个文件夹(绝对路径)内的文件压缩后的localname,在zip压缩文件
							 * 中的相对路径,必须保存正确，否则文件将不会添加到zip中，只有文件夹。此处可以保证
							 * 文件夹打包后所有文件的相对路径(即文件目录树结构)是不变的。
							 */
							$this->zip_dir[$_files.'/'.$filename] = $_new_dir;
							$this->zip->addEmptyDir($_new_dir);
							$this->addFilesToZip($_files.'/'.$filename, $_new_dir);
						} else {
							$zip_name = empty($this->zip_dir) ? $filename : $this->zip_dir[$_files].'/'.$filename;
							$this->zip->addFile($_files.'/'.$filename, $zip_name);
						}
					}
				}
				closedir($handle);
			}
		} else {
			$this->zip->addFile($_files, basename($_files));
		}
	}
	
}
?>

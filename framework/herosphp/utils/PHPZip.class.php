<?php
namespace herosphp\utils;
/**
 * PHP 生成zip压缩文件类(pack and compress the point file or direcoty to zip file) <br />
 * 支持两种形式传入文件 <br/>
 * 	1.直接传入需要打包的文件的路径( String of path) <br />
 * 	2.通过表单浏览上传多个文件进行打包 ( Array of filename ) <br />
 * *******************************************************************************
 * 许可声明：此为专门为网络星空PHP高性能建站班级量身定制的"轻量级"PHP框架
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。   
 * ********************************************************************************
 * @author	yangjian<yangjian102621@163.com>
 * @version	1.0
 * @completed	2013.03.14
 * @lastupdate  2013.-04-22
 */
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
	 * @param	$_file		file to be pack and compressed.
	 */
	public function __construct( $_file ) {
		//check the Apache server is surpport zip compress.
		if ( !@extension_loaded('zip') && !@function_exists("gzcompress") ) {
			E("当前服务器不支持压缩，请更改PHP的相关配置。");
		}
		@set_time_limit(0);
		$this->file_path = $_file;
		if ( !is_array($_file) ) {
			if ( file_exists($_file) ) {
				$path_info = pathinfo($this->file_path);
				$this->zip_name = dirname($this->file_path).'/'.$path_info['filename'].'.zip';
			} else {
				E("您要压缩的文件或目录不存在，请选择正确的文件路径。");
			}
		} else {
			//将指定的多个文件打包, 默认生成的文件名称为test.zip
			$this->file_path = $_file;
			$this->zip_name = dirname(__FILE__).'/test.zip';	
		}
	}
	
	/**
	 * create zip file method.
	 * 
	 * @param	$_zip_name	filename of zip file.
	 * @return	boolean 	TRUE for success and FALSE for faild.	
	 */
	public function createZip( $_zip_name = NULL ) {
		if ( $_zip_name != NULL ) 
			$this->zip_name = $_zip_name;
			
		//实例化压缩类，创建压缩文件
		$this->zip = new ZipArchive();
		if ( !file_exists($this->zip_name) ) {
			if ( $this->zip->open($this->zip_name, ZIPARCHIVE::CREATE) == FALSE ) {
				E("创建压缩文件失败");
			}
		} else {
			if ( $this->zip->open($this->zip_name, ZIPARCHIVE::OVERWRITE) == FALSE ) {
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

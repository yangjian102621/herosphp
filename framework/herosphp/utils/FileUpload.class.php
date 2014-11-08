<?php
/**
 * 文件上传类, 支持多文件上传不重名覆盖。支持base64编码文件上传
 * file upload class, supports multi-file upload and base64-encoded file upload.
 * *******************************************************************************
 * 许可声明：此为专门为网络星空PHP高性能建站班级量身定制的"轻量级"PHP框架
 * *******************************************************************************
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。           	
 * @author 	yangjian<yangjian102621@gmail.com>
 * @version  1.3
 * @completed 	2013.04.11
 * @last-update	2013.04.11
 * ******************************************************************************/
class FileUpload {
	
	/* file upload configs */
	private $config = NULL;
	/* max filesize of upload default : 2*1024*1024  */
	private static $Max_fsize = 2097152;
	
	/* error number of file upload */
	private $err_num = 0;
	/* error message of file upload */
	private static $err_msg = array();
	/* upload state info (上传状态映射表，国际化用户可在此添加国际化语言) */
	private static $stataInfoMap = array(
		 'ch' => array(		
		 	  '0'		=> 	'文件上传成功', 
		 	  '1'		=> 	'文件超出大小限制', 
		 	  '2'		=> 	'文件的大小超过了HTML表单指定值', 
		 	  '3'		=> 	'文件只有部分被上传',
		 	  '4'		=> 	'文件没有被上传',
		 	  '5'		=> 	'不允许的文件类型',
		 	  '6'		=> 	'上传目录创建失败',
		 	  '7'		=> 	'文件保存时出错',
		 	  '8'		=> 	'base64解码IO错误',
		 	  '9'		=>  '未知错误'
		 	),
		 'en' => array(		
		 	  '0'		=> 	'File uploaded successfully!', 
		 	  '1'		=> 	'File size exceeds the limit', 
		 	  '2'		=> 	'File size exceeds the limit of HTML',
		 	  '3'		=> 	'File is not completed uploaded.',
		 	  '4'		=> 	'File is not uploaded',
		 	  '5'		=> 	'Disallowed file types',
		 	  '6'		=> 	'Failed to make the upload directory',
		 	  '7'		=> 	'Failed to save the uploaded file.',
		 	  '8'		=> 	'base64 encode IO erro',
		 	  '9'		=>  'Unknow error!'
		 	),

	);

	/**
	 * constructor
	 * @param 		array()			$_config   
	 * @notice 		$_config keys  upload_dir, allowExt, max_szie
	 * @param 		boolean 		$_base64   is base64 file stream		
	 */
	public function __construct( &$_config ) {
		if ( !isset($_config) ) {
			trigger_error('Must pass in parameters $_config');
			return FALSE;
		}
		$this->config = $_config;
		if ( isset($_config['max_size']) ) self::$Max_fsize = $_config['max_size'];
		ini_set('upload_max_filesize', ceil(self::$Max_fsize/(1024*1024)).'M');
	}

	/**
	 * upload file method.
	 * @param 		sting 		$_field    	name of form elements.
	 * @return 		string 		$_filename     filename string of uploaded file.
	 */
	public function upload( $_field, $_base64 = false ) {
		if ( $_base64 ) {
			$_data = $_POST[$_field];
			return $this->makeBase64Image( $_data );
		}
		if ( !$this->check_upload_dir() ) {
			$this->err_num = 6;
			$this->addMessage($this->err_num, $this->$this->config['upload_dir']);
			return FALSE;
		}
		$_localFile = is_array($_FILES[$_field]['name']) ? $_FILES[$_field]['name'] : array($_FILES[$_field]['name']);
		$_tempFile = is_array($_FILES[$_field]['tmp_name']) ? $_FILES[$_field]['tmp_name'] : array($_FILES[$_field]['tmp_name']);
		$_error_no = is_array($_FILES[$_field]['error']) ? $_FILES[$_field]['error'] : array($_FILES[$_field]['error']);
		$_file_num = count($_localFile);
		$_filename = '';
		for ( $i = 0; $i < $_file_num; ++$i ) {
			$this->err_num = $_error_no[$i];
			if ( $this->err_num == 0 ) {
				$this->check_file_type($_localFile[$i]);
				if ( $this->err_num == 0 ) {
					$this->check_file_size($_tempFile[$i]);
					if ( $this->err_num == 0 ) {
						if ( is_uploaded_file($_tempFile[$i]) ) {
							$_new_filename = $this->getFileName($_localFile[$i]);
							if ( move_uploaded_file($_tempFile[$i], $this->config['upload_dir'].DIR_OS.$_new_filename) ) {
								$_filename .= $_filename == NULL ? $_new_filename : ','.$_new_filename;
							} else {
								$this->err_num = 7;
							}
						}
					}
				}
			}
			$this->addMessage($this->err_num, $_localFile[$i]);

		}
		return $_filename;
		//Utils::myPrint($_file);

	}

	/**
	 * make base64 image, convert base64 code to image
	 */
	private function makeBase64Image( $_base64_data ) {
		$_img = base64_decode($_base64_data);
		$_filename = time().rand( 1 , 1000 ).".png";
		if ( file_put_contents($this->config['upload_dir'].DIR_OS.$_filename, $_img) ) {
			return $_filename;
		}
		$this->err_num = 8;
		$this->addMessage($this->err_num, 'base64 data save faild. '.$_filename);
	}

	/* get new filename */
	private function getFileName($filename) {
		$_ext = Utils::getFileExt($filename);
		list($msec, $sec) = explode(' ', microtime());
		return $sec.'-'.substr($msec, 2, 2).'-'.mt_rand(1000, 9999).'.'.$_ext;
	}

	/* check upload dir */
	private function check_upload_dir() {
		if ( !file_exists($this->config['upload_dir']) ) {
			return Utils::makeFileDirs($this->config['upload_dir']);
		}
		return TRUE;
	}

	/* check file type */
	private function check_file_type( $filename ) {
		$_ext = Utils::getFileExt($filename);
		if ( !in_array($_ext, $this->config['allowExt']) ) {
			$this->err_num = 5;
		}
	}

	/* check filesize  */
	private function check_file_size( $filename ) {
		if ( filesize($filename) > self::$Max_fsize ) 
			$this->err_num = 1;
	}

	/**
	 * add message to error message array
	 * @param 		string 		$_error_no     error number
	 * @param  		string 		$_msg			error message
	 */
	private function addMessage( $_error_no, $_msg ) {
		self::$err_msg[$_msg] = $_error_no;
	}

	/**
	 * get upload message
	 * @param 		string  		$_lang     错误信息的语言
	 */
	public function get_upload_messgae( $_lang = 'ch' ) {
		$_err_arr = array();
		foreach ( self::$err_msg as $_key => $_val )  {
			$_err_arr[] = "错误: {$_key}, ".self::$stataInfoMap[$_lang][$_val];
		}
		return $_err_arr;
	}
	
}
?>
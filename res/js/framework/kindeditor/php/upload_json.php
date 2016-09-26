<?php
if ( !defined("ROOT") ) define("ROOT", dirname(dirname(__FILE__)));
if ( !defined("DIR_OS") ) define("DIR_OS", DIRECTORY_SEPARATOR);
//开启错误日志
ini_set("display_errors", "Off");
ini_set("log_errors", "On");
ini_set("error_log", ROOT.DIR_OS."error_log.txt");
include ROOT.DIR_OS.'php'.DIR_OS.'JSON.php';
include ROOT.DIR_OS.'php'.DIR_OS.'FileUpload.class.php';

//接收参数
$_module = trim($_GET['module']);
$_water = trim($_GET['water']);		//是否添加水印
$_dir = trim($_GET['dir']);		//上传文件类型 图片 | 文件 | 视频 | flash

//文件保存目录路径
$_upload_dir = 'uploadfiles'.DIR_OS.$_dir.DIR_OS.$_module.DIR_OS.date('Y').DIR_OS.date('m');
$_upload_url = dirname(dirname($_SERVER['PHP_SELF'])) . '/'.str_replace(DIR_OS, '/', $_upload_dir). '/';

//文件后缀名
$_allowExt = array(
	'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
	'flash' => array('swf', 'flv'),
	'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi'),
	'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
);
//最大文件大小
$_max_size = array(
	'image' => 1024*1024,		//最大上传1MB 图片
	'flash' => 1024*1024*20,	//最大20MB	flash
	'media' => 1024*1024*100,	//最大100MB	视频
	'file' => 1024*1024*20		//最大20MB	附件
);
//上传的配置文件
$_config = array(
	'upload_dir' =>  ROOT.DIR_OS.$_upload_dir,
	'allowExt' 	=> $_allowExt[$_dir],
	'max_size' 	=> $_max_size[$_dir]
);
$_file = new FileUpload( $_config, false );
$_filename = $_file->upload('imgFile');
$file_url = $_upload_url . $_filename;

//添加水印
if ( !empty($_water) && $_dir == 'image' ) {
	include ROOT.DIR_OS.'php'.DIR_OS.'Image.class.php';
	$image = Image::getInstance();
	//添加图片水印
	/*
	$_w = mt_rand(1, 3);
	$_water_img = ROOT.DIR_OS.'themes'.DIR_OS.'water'.DIR_OS.'logo_'.$_w.'.gif';
	//imageWaterMark( $_img_src, $_water_path, $_water_pos, $_water_alpha )
	$image->imageWaterMark($_config['upload_dir'].DIR_OS.$_filename, $_water_img, 5, 60);
	*/
	//添加文字水印
	
	$image->setWaterFontStyle(2);		//设置水印字体样式
	$_color = array(180,0,0);
	$_angle = mt_rand(0, 60);
	$_water = "环球塑化网";
	$image->stringWaterMark($_config['upload_dir'].DIR_OS.$_filename, $_water, 40, $_color, 5, $_angle);
	
}

header('Content-type: text/html; charset=UTF-8');
$json = new Services_JSON();
if ( $_filename == '' ) {
	$_err = $_file->get_upload_messgae('ch');
	echo $json->encode(array('error' => 1, 'message' => "文件上传失败！[{$_err[0]}]"));
} else {
	echo $json->encode(array('error' => 0, 'url' => $file_url));
}
/**
 * 创建多层文件目录
 * @param 	string 		$_path			需要创建路径
 * @param 	string 		$permission		权限
 * @return 	boolean     成功时返回TRUE，失败则返回FALSE;
 */
function makeFileDirs( $_path ) {
	//必须考虑 "/" 和 "\" 两种目录分隔符
	$files = preg_split('/\/|\\\\/s', $_path);
	$_dir = '';
	foreach ($files as $value) {
		$_dir .= $value.DIRECTORY_SEPARATOR;
		if ( !file_exists($_dir) ) {
			if ( !mkdir($_dir) ) return FALSE;
		}
	}
	return  TRUE;
}

/**
 * 获取文件后缀名称
 * @param 		string  	文件名
 */
function getFileExt( $filename ) {
	$_pos = strrpos( $filename, '.' );
	return strtolower( substr( $filename , $_pos+1 ) );
}
exit();

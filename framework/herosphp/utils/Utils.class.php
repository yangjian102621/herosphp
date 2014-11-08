<?php
/**
 * 公共工具类, 定义所有的常用工具方法
 * *******************************************************************************
 * 许可声明：此为专门为网络星空PHP高性能建站班级量身定制的"轻量级"PHP框架
 * *******************************************************************************
 * 版权所有 (C) 2013.03-2013 网络星空工作室研发中心 并保留所有权利。           	
 * @author 	yangjian<yangjian102621@gmail.com>
 * @date 	2013.04.02
 * ******************************************************************************/
class Utils {

	/**
	 * 创建多层文件目录
	 * @param 	string 		$_path			需要创建路径
	 * @param 	string 		$permission		权限
	 * @return 	boolean     成功时返回TRUE，失败则返回FALSE;
	 */
	public static function makeFileDirs( $_path, $permission=0755 ) {
		//必须考虑 "/" 和 "\" 两种目录分隔符
		$files = preg_split('/\/|\\\\/', $_path);
		$_dir = '';
		foreach ($files as $value) {
			$_dir .= $value.DIRECTORY_SEPARATOR;
			if ( !file_exists($_dir) ) {
				if ( !mkdir($_dir, $permission, true) ) return FALSE;
			}
		}
		return  TRUE;
	}

	/**
     * 获取文件后缀名称
     * @param 		string  	文件名
	 */
	public static function getFileExt( $filename ) {
		$_pos = strrpos( $filename, '.' );
		return strtolower( substr( $filename , $_pos+1 ) );
	}
	
	/**
 	 * 从网络地址中获取文件名
	 */
	public static function getFileFromUrl( $_url ) {
		$_pos1 = strrpos( $_url, '/' );
		$_pos = strpos($_url, '?');
		$_pos2 = $_pos === FALSE ? strlen($_url) : $_pos;
		return substr($_url, $_pos+1, $_pos2);
	}

	//get the client IP
	public static function getClientIP() {
		$ip = '';
		if (getenv('HTTP_CLIENT_IP')) 				$ip = getenv('HTTP_CLIENT_IP'); 
		//获取客户端用代理服务器访问时的真实ip 地址
		else if (getenv('HTTP_X_FORWARDED_FOR')) 	$ip = getenv('HTTP_X_FORWARDED_FOR');
		else if (getenv('HTTP_X_FORWARDED')) 		$ip = getenv('HTTP_X_FORWARDED');
		else if (getenv('HTTP_FORWARDED_FOR')) 		$ip = getenv('HTTP_FORWARDED_FOR'); 
		else if (getenv('HTTP_FORWARDED')) 			$ip = getenv('HTTP_FORWARDED');
		else  										$ip = $_SERVER['REMOTE_ADDR'];
		return $ip;
	}

	/**
	 * get format filesize string(获取格式化文件大小字符串)
	 * @param 	int			$size
	 * @return  string 		$size_str	
	 */
	public static function formatFileSize( $size ) {
		 if ( $size/1024 < 1 ) {
			return $size ." B";	  
		 } else if ( $size/1024 > 1 && $size/(1024*1024) < 1 ) {
			return number_format($size/1024, 2, '.', '') .'KB';	 
		 } else if ( $size/(1024*1024) > 1 && $size/(1024*1024*1024)< 1 ) {
			 return number_format($size/(1024*1024), 2, '.', '') ." MB";
		 } else {
			return number_format($size/(1024*1024*1024), 2, '.', '')." GB";	 
		 }
	 }
}
?>

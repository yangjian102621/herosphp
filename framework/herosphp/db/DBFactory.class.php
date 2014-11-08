<?php
/**
 * 数据库操作工厂类。
 * Oracle database operation class, implements class Idb.
 * ---------------------------------------------
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。 
 * ----------------------------------------------------------
 * @author 	yangjian<yangjian102621@gmail.com>
 * @version 1.1 
 * @completed	2013-04-10
 */
if ( ! defined('DB_LIB_HOME') )	
	define('DB_LIB_HOME', dirname(__FILE__) . DIRECTORY_SEPARATOR);
class DBFactory {
	
	/* if has db instance */
	private static $_DB = array();
	
	/**
	 * method to create db instance
	 * @param		string 		$_key		使用哪种方式链接数据库,默认使用mysql.
	 * @param		array 		$_config 	数据库的配置信息.  
	 */
	public static function createDB( $_key, &$_config = NULL ) {
		
		if ( empty(self::$_DB) ) {
			include DB_LIB_HOME.'Idb.class.php';
		}
		$_className = ucfirst($_key);
		$_class_file = DB_LIB_HOME.$_className.'.class.php';
		
		/**
		 * 1. 接口文件只包含一次 
		 * 2. access 改变，如要是使用oracle或者其他数据库则需要重新包含接口
		 */
		if ( !isset(self::$_DB[$_className]) ) {
			include $_class_file;
			self::$_DB[$_className] = 1;
		}
		/**
		 * 1. 通过数据库的配置信息产生key, 每个数据库只连接一次,返回唯一实例并缓存
		 * 2. 如果没有传入config则使用默认的config配置（详细见SysCfg.class.php）
		 * 3. $_config改变了, 说明更改了数据库配置，则需要重新连接
		 */
		$_db_key = $_className;		//DB缓存对象的key值
		if ( $_config ) $_db_key .= $_config['host'].$_config['user'].$_config['db'];
		if ( !isset(self::$_DB[$_db_key]) ) {
			self::$_DB[$_db_key] = new $_className($_config);
		}
		return self::$_DB[$_db_key];	
		
	}
	
}
?>
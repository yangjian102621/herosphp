<?php
/**
 * 变量和表单数据验证类 <br />
 * variable or input filter class . <br />
 * *******************************************************************************
 * 许可声明：此为专门为网络星空PHP高性能建站班级量身定制的"轻量级"PHP框架
 * *******************************************************************************
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。           	
 * @author	chenxin<chenxin619315@gmail.com>
 * @version	1.0
 * @completed 	2013.04.06
 * @last-update	2013.04.08
 */

define('SY_GET_INPUT', 0);
define('SY_POST_INPUT', 1);
define('SY_LENGHT_BOTH', 0);
define('SY_LENGHT_LEFT', 1);
define('SY_RANGE_BOTH', 2);
define('SY_RANGE_LEFT', 3);

//syrian filter type define
define('SY_NULL', 1 << 0);
define('SY_STRING', 1 << 1);			//not all white space
define('SY_NUMERIC', 1 << 2);
define('SY_LATIN', 1 << 3);
define('SY_EMAIL', 1 << 4);
define('SY_CELLPHONE', 1 << 5);
define('SY_URL', 1 << 6);
define('SY_DATE', 1 << 7);
define('SY_ZIP', 1 << 8);
define('SY_IDENTITY', 1 << 9);
define('SY_QQ', 1 << 10);

define('SY_SANITIZE_SCRIPT', 1 << 0);
define('SY_SANITIZE_HTML', 1 << 1);
define('SY_MAGIC_QUOTES', 1 << 2);
define('SY_SANITIZE_INT', 1 << 3);
define('SY_SANITIZE_TRIM', 1 << 4);
function SY_LIMIT( $l, $r = -1 ) {return ($r==-1?array(SY_LENGHT_LEFT, $l):array(SY_LENGHT_BOTH, $l, $r));}
function SY_RANGE( $l, $r = -1 ) {return ($r==-1?array(SY_RANGE_LEFT, $l):array(SY_RANGE_BOTH, $l, $r));}

class Filter {
	//all whitespace
	private static function isString( &$_str ) {
		return ($_str != '' && preg_match('/^\s{1,}$/', $_str) == 0);
	}
	//is validate basic latin
	private static function isLatin( &$_str ) {
		return (preg_match('/^[0-9a-zA-z_]{1,}$/', $_str) == 1);
	}
	//is validate email address
	private static function isEmail( &$_str ) {
		return (filter_var($_str, FILTER_VALIDATE_EMAIL) != FALSE);
	}
	//validate cellphone number
	private static function isCellphone( &$_str ) {
		return (preg_match('/^1[3|5|4|8][0-9]{9}$/', $_str) == 1);
	}
	//validate url address
	private static function isUrl( &$_str ) {
		return (filter_var($_str, FILTER_VALIDATE_URL) != FALSE);
	}
	//validate date 2013-04-12
	private static function isDate( &$_str ) {
		return (preg_match('/^[1-9][0-9]{3}-(0[1-9]|10|11|12)-([0|1|2][0-9]|30|31)$/', $_str) == 1);
	}
	//validate zip code
	private static function isZip( &$_str ) {
		return (preg_match('/^[1-6][1-9][0-9]{4}$/', $_str) == 1);
	}
	//validate identity card
	private static function isIdentity( &$_str ) {
		return (preg_match('/^[1-6][0-9]{5}[1|2][0-9]{3}(0[1-9]|10|11|12)([0|1|2][0-9]|30|31)[0-9]{3}[0-9A-Z]$/', $_str) == 1);
	}
	//validate qq number
	private static function isQQ( &$_str ) {
		return (preg_match('/^[1-9][0-9]{4,15}$/', $_str) == 1);
	}
	
	//script sanitize
	private static function sanitizeScript( &$_str ) {
		$_rules = array(
			'/<script(.*?)>(.*?)<\/script\s*>/i',
			'/<script(.*?)\/>/i');
		$_str = preg_replace($_rules, array('', ''), $_str);
	}
	
	/**
	 * load from variable .<br />
	 *
	 * @param	$_var
	 * @param	$_model
	 * @param	$_error
	*/
	public static function loadArrayFromVar( &$_src, $_model, &$_error, $_quote = false ) {
		$_ret = array();
		$_addslashes = (ini_get('magic_quotes_gpc') == 0);
		foreach ( $_model as $_key => $_val ) {
			$_error[0] = $_key;
			$_error[1] = 0;
			//check the allocation of the value
			if ( $_val[0] == NULL ) $_val[0] = SY_STRING;
			if ( ! isset( $_src[$_key] ) ) {
				if ( ($_val[0] & SY_NULL) != 0 ) continue;	//could be NULL
				return FALSE;
			}
			//data type check
			if ( ( $_val[0] & SY_STRING )
						&& ! self::isString( $_src[$_key] ) )	return FALSE;
			if ( ( $_val[0] & SY_NUMERIC ) != 0
						&& ! is_numeric( $_src[$_key] ) ) 		return FALSE;
			if ( ( $_val[0] & SY_LATIN ) != 0
						&& ! self::isLatin($_src[$_key]) )		return FALSE;
			if ( ( $_val[0] & SY_EMAIL ) != 0
						&& ! self::isEmail($_src[$_key]) )		return FALSE;
			if ( ( $_val[0] & SY_CELLPHONE ) != 0
						&& ! self::isCellphone($_src[$_key]) )	return FALSE;
			if ( ( $_val[0] & SY_URL ) != 0
						&& ! self::isUrl($_src[$_key]))			return FALSE;
			if ( ( $_val[0] & SY_DATE ) != 0
						&& ! self::isDate($_src[$_key]))		return FALSE;
			if ( ( $_val[0] & SY_ZIP ) != 0
						&& ! self::isZip($_src[$_key]) )		return FALSE;
			if ( ( $_val[0] & SY_IDENTITY ) != 0
						&& ! self::isIdentity($_src[$_key]) )	return FALSE;
			if ( ( $_val[0] & SY_QQ ) != 0
						&& ! self::isQQ($_src[$_key]) )			return FALSE;
			
			//limit check
			$_error[1] = 1;
			if ( $_val[1] != NULL ) {
			switch ( $_val[1][0] ) {
				case SY_LENGHT_BOTH:
					$_length = strlen($_src[$_key]);
					if ( $_length < $_val[1][1] || $_length > $_val[1][2] ) return FALSE;
					break;
				case SY_LENGHT_LEFT:
					$_length = strlen($_src[$_key]);
					if ( $_length < $_val[1][1] ) return FALSE;
					break;
				case SY_RANGE_BOTH:
					if ( $_src[$_key] < $_val[1][1] || $_src[$_key] > $_val[1][2] ) return FALSE;
					break;
				case SY_RANGE_LEFT:
					if ( $_src[$_key] < $_val[1][1] ) return FALSE;
					break;
				}
			}
			
			if ( $_quote ) $_ret[$_key] = &$_src[$_key];
			else $_ret[$_key] = $_src[$_key];
			if ( $_val[2] == NULL ) continue;
			
			//sanitize
			if ( ( $_val[2] & SY_SANITIZE_SCRIPT ) != 0 ) self::sanitizeScript($_ret[$_key]);
			if ( ( $_val[2] & SY_SANITIZE_HTML ) != 0 )	$_ret[$_key] = htmlspecialchars($_ret[$_key]);
			if ( ( $_val[2] & SY_MAGIC_QUOTES ) != 0 && $_addslashes )	$_ret[$_key] = addslashes($_ret[$_key]);
			if ( ( $_val[2] & SY_SANITIZE_INT ) != 0 )	$_ret[$_key] = intval($_ret[$_key]);
			if ( ( $_val[2] & SY_SANITIZE_TRIM ) != 0 ) $_ret[$_key] = trim($_ret[$_key]);
		}
		
		foreach ( $_src as $_name => $_value ) {
			if ( isset($_ret[$_name]) ) continue;
			$_ret[$_name]  = $_value;
		}
		
		return $_ret;
	}
	
	/**
	 * load from input . <br />
	 *
	 * @param	$_input
	 * @param	$_model
	 * @param	$_error
	*/
	public static function loadArrayFromInput( $_input, $_model, &$_error, $_quote = false) {
		switch ( $_input ) {
		case SY_GET_INPUT:	return self::loadArrayFromVar( $_GET, $_model, $_error, $_quote );
		case SY_POST_INPUT:	return self::loadArrayFromVar( $_POST, $_model, $_error, $_quote );
		}
		
		return FALSE;
	}
}
?>
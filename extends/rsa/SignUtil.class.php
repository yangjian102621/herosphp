<?php
namespace rsa;

/**
 * 签名类
 */
class SignUtil
{

	public static $unSignKeyList = array(
		"__version" => 1,
		"__sign" => 1
	);

	public static function signWithoutToHex($params)
	{
		ksort($params);
		$sourceSignString = SignUtil::signString($params, SignUtil::$unSignKeyList);
		error_log($sourceSignString, 0);
		$sha256SourceSignString = hash("sha256", $sourceSignString, true);
		error_log($sha256SourceSignString, 0);
		$RSACrypt = new RSACrypt();
		return $RSACrypt->encryptByPrivateKey($sha256SourceSignString);
	}

	public static function sign($url, $params)
	{
		ksort($params);
		$sourceSignString = $url.self::signString($params);
		return md5($sourceSignString);
	}


	public static function signString($params)
	{

		if( is_string($params) ) {
			return $params;
		}
		//拼原String
		$newparams = array();
		//保留需要参与签名的属性
		foreach ( $params as $key => $value ) {
			if ( !isset(self::$unSignKeyList[$key]) ) {
				$newparams[] = $value;
			}
		}
		return json_encode($newparams ,JSON_UNESCAPED_UNICODE);
	}
}

<?php
namespace herosphp\http;

/**
 * 发送http请求类
 * Class HttpClient
 * @package herosphp\http
 */
class HttpClient {

	/**
	 * 发送 http GET 请求
	 * @param $url
	 * @param $params
	 * @param null $headers 请求头信息
	 * @param null $setting curl设置
	 * @param bool $return_header 是否返回头信息
	 * @return array|bool|mixed
	 */
	public static function get( $url, $params=null, $headers=null, $setting=null, $return_header = false )
	{
		$curl = curl_init();
		if( stripos($url, 'https://') !== false ) { //支持https请求
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}

		if ( is_array($params) ) {
			$params = http_build_query($params);
		}
		if ( $params ) {
			if ( strpos($url, '?') == false ) {
				$url .= '?'.$params;
			} else {
				$url .= '&'.$params;
			}
		}

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		if ( is_array($headers) ) {
			$_headers = array();
			foreach ( $headers as $key => $value ) {
				$_headers[] = "{$key}:$value";
			}
			curl_setopt($curl, CURLOPT_HTTPHEADER, $_headers);
		}

		//check and apply the setting
		if ( $setting != NULL ) {
			foreach ( $setting as $key => $val ) {
				curl_setopt($curl, $key, $val);
			}
		}

		$ret = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);

		if(  $return_header ) {
			return array(
				'header' => $info,
				'body'   => $ret
			);
		}

		if( intval( $info["http_code"] ) == 200 ) {
			return $ret;
		}

		return false;
	}

	/**
	 * 发送http POST 请求
	 * @param $url
	 * @param $params
	 * @param null $headers
	 * @param null $setting
	 * @return bool|mixed
	 */
	public static function post( $url, $params, $headers=null, $setting=null )
	{
		$curl	= curl_init();
		if( stripos( $url, 'https://') !== FALSE ) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		if ( is_array($params) ) {
			$params = http_build_query($params);
		}
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		if ( is_array($headers) ) {
			$_headers = array();
			foreach ( $headers as $key => $value ) {
				$_headers[] = "{$key}:$value";
			}
			curl_setopt($curl, CURLOPT_HTTPHEADER, $_headers);
		}

		//check and apply the setting
		if ( $setting != null ) {
			foreach ( $setting as $key => $val ) {
				curl_setopt($curl, $key, $val);
			}
		}

		$ret	= curl_exec($curl);
		$info	= curl_getinfo($curl);

		curl_close($curl);

		if( intval($info['http_code']) == 200 ) {
			return $ret;
		}

		return false;
	}
}
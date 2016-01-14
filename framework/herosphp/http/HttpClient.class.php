<?php
namespace herosphp\http;

/**
 * 发送http请求类
 * Class HttpClient
 * @package herosphp\http
 */
class HttpClient {
	
	/**
	 * Set timeout default.
	 */
	private $timeout = 30;
	
	/**
	 * Set connect timeout.
	 */
    private $connecttimeout = 30;
	
	/**
	 * boundary of multipart
	 */
    private static $boundary = '';
	
	/**
	 * http respose code
	 * @var string
	 */
    private $http_code;
	
	/**
	 * http response info
	 */
    private $http_info = null;

    private $http_error = null;

    /**
     * Post a http request
     * @param $url
     * @param $parameters
     * @param bool $multi
     * @return string
     */
	public function post($url, $parameters, $multi=false){
		return $this->request($url, $method='POST', $parameters, $multi);
	}

    /**
     * Get a http request
     * @param $url
     * @param $parameters
     * @return string
     */
	public function get($url, $parameters){
		return $this->request($url, $method='GET', $parameters);
	}

    /**
     * Send a Http request
     * @param $url
     * @param string $method
     * @param $parameters
     * @param bool $multi
     * @return string
     */
	public function request($url, $method='POST', $parameters, $multi = false) {

		switch ($method) {
			case 'GET' :
				$url = $url . '?' . http_build_query ( $parameters );
				return $this->http($url, 'GET' );
			default :
				$headers = array ();
				if (! $multi && (is_array ($parameters ) || is_object ( $parameters ))) {
					$body = http_build_query ( $parameters );
				} else {
					$body = self::build_http_query_multi ( $parameters );
					$headers [] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
				}
				return $this->http($url, $method, $body, $headers);
		}
	}

    /**
     * Make an HTTP request
     * @param $url
     * @param $method
     * @param null $postfields
     * @param array $headers
     * @return string
     * @ignore
     */
	private function http($url, $method, $postfields = NULL, $headers = array()) {

		$this->http_info = array();
		$ci = curl_init();
		/* Curl settings */
		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_HEADER, FALSE);

		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($postfields)) {
					$url = "{$url}?{$postfields}";
				}
		}

		$headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
		curl_setopt($ci, CURLOPT_URL, $url );
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);

		$response = curl_exec($ci);
		$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_error = curl_error($ci);
		$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
		curl_close ($ci);
		return $response;
	}

	private static function build_http_query_multi($params) {
		if (!$params) return '';

		uksort($params, 'strcmp');

		self::$boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

		foreach ($params as $parameter => $value) {

			if( in_array($parameter, array('pic', 'image')) && $value{0} == '@' ) {
				$url = ltrim( $value, '@' );
				$content = file_get_contents( $url );
				$array = explode( '?', basename( $url ) );
				$filename = $array[0];

				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
				$multipartbody .= "Content-Type: image/unknown\r\n\r\n";
				$multipartbody .= $content. "\r\n";
			} else {
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
				$multipartbody .= $value."\r\n";
			}

		}

		$multipartbody .= $endMPboundary;
		return $multipartbody;
	}
}
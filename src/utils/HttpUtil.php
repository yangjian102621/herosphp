<?php


// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\utils;

use CurlHandle;
use herosphp\exception\HeroException;

/**
 * 发送 http 请求类
 * -----------------------
 * @author RockYang<yangjian102621@gmail.com>
 */

class HttpUtil
{
    // curl handle
    private CurlHandle $_handler;

    // if return the header message of response
    private bool $_return_header = false;

    public static function init(): HttpUtil
    {
        return new static();
    }

    private function __construct()
    {
        $handler = curl_init();

        curl_setopt($handler, CURLOPT_HEADER, 0);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);

        $this->_handler = $handler;
    }

    public function headers(array $headers): HttpUtil
    {
        if (is_array($headers)) {
            $_headers = [];
            foreach ($headers as $key => $value) {
                $_headers[] = "{$key}:$value";
            }
            curl_setopt($this->_handler, CURLOPT_HTTPHEADER, $_headers);
        }
        return $this;
    }

    public function proxy(string $ip, int $port)
    {
        curl_setopt($this->_handler, CURLOPT_PROXY, $ip);
        curl_setopt($this->_handler, CURLOPT_PROXYPORT, $port);
    }

    public function get(string $url, array $params,)
    {
        $params = http_build_query($params);
        if (strpos($url, '?') == false) {
            $url .= '?' . $params;
        } else {
            $url .= '&' . $params;
        }

        curl_setopt($this->_handler, CURLOPT_HTTPGET, true);
        return $this->_doRequest($url);
    }


    /**
     * 发送http POST 请求
     * @param $url
     * @param $params
     * @param null $headers
     * @return bool|mixed
     */
    public static function post($url, $params, $headers = null)
    {
        $self = new self();
        if (is_array($params)) {
            $params = http_build_query($params);
        }
        $curl = $self->_curlInit($url, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        return $self->_doRequest($curl, false);
    }

    /**
     * 发送restful PUT请求
     * @param $url
     * @param $params
     * @return mixed
     */
    public static function put($url, $params)
    {
        $self = new self();
        if (is_array($params)) {
            $params = StringUtils::jsonEncode($params);
        }
        $curl = $self->_curlInit($url, ['Content-Type' => 'application/json']);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        return $self->_doRequest($curl, false);
    }

    /**
     * 发送restful DELETE请求
     * @param $url
     * @param $params
     * @return mixed
     */
    public static function delete($url, $params)
    {
        $self = new self();
        if (is_array($params)) {
            $params = StringUtils::jsonEncode($params);
        }
        $curl = $self->_curlInit($url, ['Content-Type' => 'application/json']);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        return $self->_doRequest($curl, false);
    }


    private function _doRequest(string $url)
    {
        curl_setopt($this->_handler, CURLOPT_URL, $url);
        $ret = curl_exec($this->_handler);
        $info = curl_getinfo($this->_handler);

        curl_close($this->_handler);
        if ($ret == false) {
            throw new HeroException('cURLException:' . curl_error($this->_handler));
        }

        if ($this->_return_header) {
            return ['header' => $info, 'body' => $ret];
        }
        return $ret;
    }

    /**
     * 创建curl对象
     * @param $url
     * @param $headers
     * @return resource
     */
    private static function _curlInit($url, $headers)
    {
        $curl = curl_init();
        if (stripos($url, 'https://') !== false) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if (is_array($headers)) {
            $_headers = [];
            foreach ($headers as $key => $value) {
                $_headers[] = "{$key}:$value";
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $_headers);
        }
        return $curl;
    }
}

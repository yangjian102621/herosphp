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

    private array $_headers = [];

    // if return the header message of response
    private bool $_return_header = false;

    private function __construct()
    {
        $handler = curl_init();

        curl_setopt($handler, CURLOPT_HEADER, 0);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);

        $this->_handler = $handler;
    }

    public static function init(): self
    {
        return new static();
    }

    public function headers(array $headers): self
    {
        $this->_headers = array_merge($this->_headers, $headers);
        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->_headers[$name] = $value;
        return $this;
    }

    public function proxy(string $ip, int $port): self
    {
        curl_setopt($this->_handler, CURLOPT_PROXY, $ip);
        curl_setopt($this->_handler, CURLOPT_PROXYPORT, $port);
        return $this;
    }

    public function get(string $url, ?array $params = null)
    {
        if ($params) {
            $params = http_build_query($params);
            if (str_contains($url, '?')) {
                $url .= '?' . $params;
            } else {
                $url .= '&' . $params;
            }
        }

        curl_setopt($this->_handler, CURLOPT_HTTPGET, true);
        return $this->_doRequest($url);
    }

    public function post(string $url, ?array $params = null)
    {
        curl_setopt($this->_handler, CURLOPT_POST, true);
        if ($params) {
            curl_setopt($this->_handler, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        return $this->_doRequest($url);
    }

    public function put(string $url, ?array $params = null)
    {
        $this->header('Content-Type', 'application/json');
        curl_setopt($this->_handler, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($params) {
            curl_setopt($this->_handler, CURLOPT_POSTFIELDS, StringUtil::jsonEncode($params));
        }

        return $this->_doRequest($url);
    }

    public function delete(string $url, ?array $params = null)
    {
        $this->header('Content-Type', 'application/json');
        curl_setopt($this->_handler, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if ($params) {
            curl_setopt($this->_handler, CURLOPT_POSTFIELDS, StringUtil::jsonEncode($params));
        }

        return $this->_doRequest($url);
    }

    public function patch(string $url, ?array $params = null)
    {
        $this->header('Content-Type', 'application/json');
        curl_setopt($this->_handler, CURLOPT_CUSTOMREQUEST, 'PATCH');
        if ($params) {
            curl_setopt($this->_handler, CURLOPT_POSTFIELDS, StringUtil::jsonEncode($params));
        }
        return $this->_doRequest($url);
    }

    private function _doRequest(string $url): mixed
    {
        curl_setopt($this->_handler, CURLOPT_URL, $url);

        if (!empty($this->_headers)) {
            $headers = [];
            foreach ($this->_headers as $key => $value) {
                $headers[] = "{$key}:$value";
            }
            curl_setopt($this->_handler, CURLOPT_HTTPHEADER, $headers);
        }

        if (stripos($url, 'https://') !== false) {
            curl_setopt($this->_handler, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->_handler, CURLOPT_SSL_VERIFYHOST, false);
        }

        $ret = curl_exec($this->_handler);
        curl_close($this->_handler);
        if ($ret == false) {
            throw new HeroException('cURLException:' . curl_error($this->_handler));
        }

        if ($this->_return_header) {
            $info = curl_getinfo($this->_handler);
            return ['header' => $info, 'body' => $ret];
        }
        return $ret;
    }
}

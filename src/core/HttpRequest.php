<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
declare(strict_types=1);

namespace herosphp\core;

use Workerman\Protocols\Http\Request;
use Workerman\Worker;

/**
 * web http request wrapper class
 * @author RockYang<yangjian102621@gmail.com>
 */
class HttpRequest extends Request
{
    // Session instance
    protected ?Session $_session = null;

    protected SessionError $_sess_errno = SessionError::OK;

    // Get session
    // You should pass the $userId for user login
    public function session($uid = null): Session | null
    {
        if ($this->_session === null) {
            $this->_session = new Session();
            $sessToken = $this->startSession($uid);
            if ($sessToken === false) {
                // delete response cookie
                $this->setCookie(null, true);
                return null;
            }

            // set response cookie
            $this->setCookie($sessToken);
        }
        return $this->_session;
    }

    public function setCookie($value, $remove = false)
    {
        $cookieParams = Session::getCookieParams();
        if ($remove) {
            $cookieParams['lifetime'] = -1;
        }

        $this->connection->__header['Set-Cookie'] = [Session::$name . '=' . $value
            . (empty($cookieParams['domain']) ? '' : '; Domain=' . $cookieParams['domain'])
            . (empty($cookieParams['lifetime']) ? '' : '; Max-Age=' . $cookieParams['lifetime'])
            . (empty($cookieParams['path']) ? '' : '; Path=' . $cookieParams['path'])
            . (empty($cookieParams['samesite']) ? '' : '; SameSite=' . $cookieParams['samesite'])
            . (!$cookieParams['secure'] ? '' : '; Secure')
            . (!$cookieParams['httponly'] ? '' : '; HttpOnly')];
    }

    // start Session
    public function startSession($uid = null): mixed
    {
        if ($this->connection === null) {
            Worker::safeEcho('Request->session() fail, header already send');
            $this->_sess_errno = SessionError::ERR_LOSE_CONNECT;
            return false;
        }

        $sessConfig = Session::$config;
        $seed = null;
        $addr = $this->connection->getRemoteIp();
        $device = $this->header('user-agent');
        // old session
        $sessToken = $this->get(Session::$name) ?? $this->post(Session::$name) ??
            $this->header(Session::$name) ?? $this->cookie(Session::$name);
        if ($sessToken) {
            $token = @json_decode(@base64_decode($sessToken), true);
            if (empty($token)) {
                $this->_sess_errno = SessionError::ERR_INVALID_SESS_TOKEN;
                return false;
            }

            // check the token keys
            // format: {"uid":"289","seed":"1661999704.960527","addr":1032967450,"sign":"102516669ed4de26576fe4ed6f464bacd76a717b510d1251fe0fffc7fc00ced2"}
            foreach (['uid', 'seed', 'addr', 'sign'] as $val) {
                if (!isset($token[$val])) {
                    $this->_sess_errno = SessionError::ERR_INVALID_SESS_TOKEN;
                    return false;
                }
            }

            // check token sign
            $sign = Session::buildSign((string)$token['uid'], $token['seed'], $token['addr']);
            if (strcmp($sign, $token['sign']) !== 0) {
                $this->_sess_errno = SessionError::ERR_INVALID_SESS_TOKEN;
                return false;
            }

            // check if the ip address is changed
            if ($addr != $token['addr']) {
                $this->_sess_errno = SessionError::ERR_ADDR_CHANGED;
                return false;
            }

            $uid = $token['uid'];
            $seed = $token['seed'];
        } else { // create new session
            if ($uid === null) {
                $uid = static::createSessionId();
            }
            $seed = sprintf('%.6f', microtime(true));
            // build session token
            $sign = Session::buildSign((string)$uid, $seed, $addr);
            $sessToken = base64_encode(json_encode(['uid' => $uid, 'seed' => $seed, 'addr' => $addr, 'sign' => $sign]));
        }

        $sessionId = md5($uid . $sessConfig['private_key']);
        $this->_session->start($seed, $sessionId);

        $this->_session->addClient([
            'uid' => $uid,
            'addr' => $addr,
            'device' => $device,
            'status' => Session::C_STATUS_OK,
            'created-at' => time()
        ]);

        $client = $this->_session->getClient($seed);
        // check if the client is offline
        if ($client['status'] === Session::C_STATUS_OFF) {
            $this->_sess_errno = SessionError::ERR_PUSHED_OFFLINE;
            return false;
        }

        // check if the device change
        if (strcmp($client['device'], $device) !== 0) {
            $this->_sess_errno = SessionError::ERR_DEVICE_CHANGED;
            return false;
        }

        return $sessToken;
    }

    public function getSessionErrNo()
    {
        return $this->_sess_errno;
    }

    /**
     * @return bool
     */
    public function expectsJson(): bool
    {
        return ($this->isAjax() && !$this->isPjax()) || $this->acceptJson();
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * @return bool
     */
    public function isPjax(): bool
    {
        return (bool)$this->header('X-PJAX');
    }

    /**
     * @return bool
     */
    public function acceptJson(): bool
    {
        return str_contains($this->header('accept', ''), 'json');
    }

    /**
     * upload file
     * @param string $name
     * @return null|UploadFile[]|UploadFile
     */
    public function upload(string $name): array|UploadFile|null
    {
        $files = parent::file($name);
        if (null === $files) {
            return  null;
        }
        if (is_array(current($files))) {
            return $this->parseFiles($files);
        }
        return $this->parseFile($files);
    }

    /**
     * @param array $files
     * @return array
     */
    protected function parseFiles(array $files): array
    {
        $uploadFiles = [];
        foreach ($files as $key => $file) {
            if (is_array(current($file))) {
                $uploadFiles[$key] = $this->parseFiles($file);
            } else {
                $uploadFiles[$key] = $this->parseFile($file);
            }
        }
        return $uploadFiles;
    }

    /**
     * @param $file
     * @return UploadFile
     */
    protected function parseFile($file): UploadFile
    {
        return new UploadFile($file['tmp_name'], $file['name'], $file['type'], $file['error']);
    }
}

<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
declare(strict_types=1);

namespace herosphp\core;

use herosphp\exception\SessionException;
use Workerman\Protocols\Http\Request;
use Workerman\Worker;

/**
 * web http request wrapper class
 * @author RockYang<yangjian102621@gmail.com>
 */
class HttpRequest extends Request
{
    // Get session
    public function session($uid = null)
    {
        if ($this->session === null) {
            $sessionId = $this->_sessionId();
            if ($sessionId === false) {
                throw new SessionException('Failed to get Session ID.');
            }
            $this->session = new Session($sessionId);
            // set response cookie
            $cookie_params = Session::getCookieParams();
            $this->connection->__header['Set-Cookie'] = [Session::$name . '=' . $sessionId
                . (empty($cookie_params['domain']) ? '' : '; Domain=' . $cookie_params['domain'])
                . (empty($cookie_params['lifetime']) ? '' : '; Max-Age=' . $cookie_params['lifetime'])
                . (empty($cookie_params['path']) ? '' : '; Path=' . $cookie_params['path'])
                . (empty($cookie_params['samesite']) ? '' : '; SameSite=' . $cookie_params['samesite'])
                . (!$cookie_params['secure'] ? '' : '; Secure')
                . (!$cookie_params['httponly'] ? '' : '; HttpOnly')];
        }
        return $this->session;
    }

    // Get session id
    protected function _sessionId(): string
    {
        $sessionId = $this->get(Session::$name) ?? $this->post(Session::$name) ??
            $this->header(Session::$name) ?? $this->cookie(Session::$name);

        if (empty($sessionId)) {
            if ($this->connection === null) {
                Worker::safeEcho('Request->session() fail, header already send');
                return false;
            }

            $sessionId = static::createSessionId();
        }

        return $sessionId;
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
}

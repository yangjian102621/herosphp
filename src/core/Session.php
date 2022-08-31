<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\core;

use herosphp\exception\SessionException;
use Workerman\Protocols\Http\Session\FileSessionHandler;
use Workerman\Protocols\Http\Session\SessionHandlerInterface;

/**
 * Class Session
 */
class Session
{
    // error codes
    const INVALID_SESSION_ID = 1 << 0;

    // Session name
    public static $name = 'heros-sess-token';

    // Auto update timestamp
    public static $autoUpdateTimestamp = false;

    // Session lifetime
    public static int $lifetime = 1440;

    // Cookie lifetime
    public static int $cookieLifetime = 1440;

    // Session cookie path
    public static string $cookiePath = '/';

    // Session cookie domain
    public static string $domain = '';

    // HTTPS only cookies
    public static bool $secure = false;

    // HTTP access only
    public static bool $httpOnly = true;

    // Same-site cookies
    public static string $sameSite = '';

    // Gc probability
    public static array $gcProbability = [1, 1000];

    // Session andler class which implements SessionHandlerInterface
    protected static string $_handlerClass = FileSessionHandler::class;

    // Session config
    protected static array $_config = [];

    // Session handler instance
    protected static ?object $_handler = null;

    // Session data
    protected array $_data = [];

    // Session changed and need to save
    protected $_needSave = false;

    // Session id
    protected $_sessionId = null;

    // Session constructor
    public function __construct(string $session_id)
    {
        static::checkSessionId($session_id);
        if (static::$_handler === null) {
            static::initHandler();
        }
        $this->_sessionId = $session_id;
        if ($data = static::$_handler->read($session_id)) {
            $this->_data = unserialize($data);
        }
    }

    public function __destruct()
    {
        $this->save();
        if (random_int(1, static::$gcProbability[1]) <= static::$gcProbability[0]) {
            $this->gc();
        }
    }

    // Get session id
    public function getId()
    {
        return $this->_sessionId;
    }

    // Get session
    public function get(string $name, mixed $default = null): mixed
    {
        return isset($this->_data[$name]) ? $this->_data[$name] : $default;
    }

    // Store data in the session
    public function set(string $name, mixed $value): void
    {
        $this->_data[$name] = $value;
        $this->_needSave = true;
    }

    // Delete an item from the session
    public function delete(string $name)
    {
        unset($this->_data[$name]);
        $this->_needSave = true;
    }

    // get and delete an item from the session
    public function take(string $name, mixed $default = null): mixed
    {
        $value = $this->get($name, $default);
        $this->delete($name);
        return $value;
    }

    // Remove all data from the session
    public function clear(): void
    {
        $this->_needSave = true;
        $this->_data = [];
    }

    // Save session to store
    public function save(): void
    {
        if ($this->_needSave) {
            if (empty($this->_data)) {
                static::$_handler->destroy($this->_sessionId);
            } else {
                static::$_handler->write($this->_sessionId, serialize($this->_data));
            }
        } elseif (static::$autoUpdateTimestamp) {
            static::refresh();
        }
        $this->_needSave = false;
    }

    // Refresh session expire time
    public function refresh()
    {
        static::$_handler->updateTimestamp($this->getId());
    }

    // Init session
    public static function init(): void
    {
        if (($gc_probability = (int)ini_get('session.gc_probability')) && ($gc_divisor = (int)ini_get('session.gc_divisor'))) {
            static::$gcProbability = [$gc_probability, $gc_divisor];
        }

        $config = Config::get('session');
        if (!empty($config['handler_class'])) {
            static::$_handlerClass = $config['handler_class'];
        }

        if ($config['lifetime'] > 0) {
            static::$lifetime = $config['lifetime'];
        } elseif ($gc_max_life_time = ini_get('session.gc_maxlifetime')) {
            static::$lifetime = (int)$gc_max_life_time;
        }

        static::$cookieLifetime = $config['lifetime'];
        static::$cookiePath = isset($config['secure']) ?? '/';
        static::$secure = isset($config['secure']) ?? false;
        static::$httpOnly = isset($config['httponly']) ?? true;
        if (!empty($config['domain'])) {
            static::$domain = $config['domain'];
        }

        static::$_config = $config;
    }

    // Get cookie params
    public static function getCookieParams()
    {
        return [
            'lifetime' => static::$cookieLifetime,
            'path' => static::$cookiePath,
            'domain' => static::$domain,
            'secure' => static::$secure,
            'httponly' => static::$httpOnly,
            'samesite' => static::$sameSite,
        ];
    }

    // GC sessions
    public function gc()
    {
        static::$_handler->gc(static::$lifetime);
    }

    // Init session store handler
    protected static function initHandler()
    {
        if (static::$_config['handler_config'] === null) {
            static::$_handler = new static::$_handlerClass();
        } else {
            static::$_handler = new static::$_handlerClass(static::$_config['handler_config']);
        }
    }

    /**
     * Check session id: TODO
     * 1. check if the session_id is valid
     * 2. check if the session_id is expired
     * 3. check if client info is changed
     */
    protected static function checkSessionId(string $session_id)
    {
        if (!\preg_match('/^[a-zA-Z0-9]+$/', $session_id)) {
            throw new SessionException("session_id $session_id is invalid");
        }
    }
}

// Init session.
Session::init();

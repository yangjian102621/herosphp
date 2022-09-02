<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\core;

use Workerman\Protocols\Http\Session\FileSessionHandler;

/**
 * Class Session
 */
class Session
{

    const FIELD_CLIENTS = '__clients__';

    const C_STATUS_OK = 1;

    const C_STATUS_OFF = 0; // client is offline

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

    // Session config
    public static array $config = [];

    // Session andler class which implements SessionHandlerInterface
    protected static string $_handlerClass = FileSessionHandler::class;

    // Session handler instance
    protected static ?object $_handler = null;

    // Session data
    protected array $_data = [];

    // Session changed and need to save
    protected bool $_needSave = false;

    // Session id
    protected ?string  $_sessionId = null;

    // Seed for client
    protected string $_seed;

    public function __destruct()
    {
        $this->save();
        if (random_int(1, static::$gcProbability[1]) <= static::$gcProbability[0]) {
            $this->gc();
        }
    }

    // Start Session
    public function start(string $seed, string $sessionId)
    {
        if (static::$_handler === null) {
            static::initHandler();
        }
        $this->_seed = $seed;
        $this->_sessionId = $sessionId;
        if ($data = static::$_handler->read($sessionId)) {
            $this->_data = unserialize($data);
        }
        if (!isset($this->_data[static::FIELD_CLIENTS])) {
            $this->_data[static::FIELD_CLIENTS] = [];
        }
    }

    // Get session id
    public function getId()
    {
        return $this->_sessionId;
    }

    // Get session seed
    public function getSeed(): string
    {
        return $this->_seed;
    }

    // Register a new client
    public function addClient(array $client)
    {
        if (isset($this->_data[static::FIELD_CLIENTS][$this->_seed])) {
            return;
        }

        if (
            static::$config['max_clients'] > 0 &&
            count($this->_data[static::FIELD_CLIENTS]) >= static::$config['max_clients']
        ) {
            $keys = array_keys($this->_data[static::FIELD_CLIENTS]);
            $offSeed = '';
            foreach ($this->_data[static::FIELD_CLIENTS] as $key => $val) {
                if ($val['status'] === static::C_STATUS_OK) {
                    $offSeed = $key;
                    break;
                }
            }
            // mark this client offline
            if ($offSeed !== '') {
                $this->_data[static::FIELD_CLIENTS][$offSeed]['status'] = static::C_STATUS_OFF;
            }
        }

        $this->_data[static::FIELD_CLIENTS][$this->_seed] = $client;
        $this->_needSave = true;
    }

    // Get the client with seed
    public function getClient(string $seed)
    {
        if (!isset($this->_data[static::FIELD_CLIENTS][$seed])) {
            return false;
        }
        return $this->_data[static::FIELD_CLIENTS][$seed];
    }

    // Remove the specified client
    public function removeClient(string $seed): void
    {
        unset($this->_data[static::FIELD_CLIENTS][$seed]);
        $this->_needSave = true;
    }

    public function getAllClients(): array
    {
        return $this->_data[static::FIELD_CLIENTS];
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
        if (($gcProbability = (int)ini_get('session.gc_probability')) && ($gcDivisor = (int)ini_get('session.gc_divisor'))) {
            static::$gcProbability = [$gcProbability, $gcDivisor];
        }

        $config = Config::get('session');
        if (!empty($config['handler_class'])) {
            static::$_handlerClass = $config['handler_class'];
        }

        if ($config['lifetime'] > 0) {
            static::$lifetime = $config['lifetime'];
        } elseif ($gcMaxLifetime = ini_get('session.gc_maxlifetime')) {
            static::$lifetime = (int)$gcMaxLifetime;
        }

        static::$cookieLifetime = $config['lifetime'];
        static::$cookiePath = isset($config['cookie_path']) ? $config['cookie_path'] : '/';
        static::$secure = isset($config['secure']) ? $config['secure'] : false;
        static::$httpOnly = isset($config['httponly']) ? $config['httponly'] : true;
        if (!empty($config['domain'])) {
            static::$domain = $config['domain'];
        }

        static::$config = $config;
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
        // clean offline clients
        foreach ($this->_data[static::FIELD_CLIENTS] as $key => $val) {
            if ($val['status'] === static::C_STATUS_OFF) {
                unset($this->_data[static::FIELD_CLIENTS][$key]);
            }
        }
    }

    public static function buildSign(string $uid, string $seed, $addr)
    {
        $data = sprintf('%s-%s-%s-%s', $uid, static::$config['private_key'], $seed, $addr);
        return sha1($data);
    }

    // Init session store handler
    protected static function initHandler()
    {
        if (static::$config['handler_config'] === null) {
            static::$_handler = new static::$_handlerClass();
        } else {
            static::$_handler = new static::$_handlerClass(static::$config['handler_config']);
        }
    }
}

// Init session.
Session::init();

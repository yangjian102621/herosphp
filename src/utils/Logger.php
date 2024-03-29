<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\utils;

use herosphp\GF;
use Throwable;

/**
 * 日志工具类
 * ---------------------------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class Logger
{
    protected static string $_log_dir;

    protected static bool $_debug = false;

    public static function init(): void
    {
        static::$_log_dir = GF::getAppConfig('log_path');
        if (!file_exists(static::$_log_dir)) {
            FileUtil::makeFileDirs(static::$_log_dir);
        }

        if (GF::getAppConfig('debug')) {
            static::$_debug = true;
        }
    }

    public static function info(mixed $message): void
    {
        static::_log('info', $message);
    }

    public static function warn(mixed $message): void
    {
        static::_log('warn', $message);
    }

    public static function error(mixed $message): void
    {
        static::_log('error', $message);
    }

    // enable|disabled debug mode
    public static function debug(mixed $debug): void
    {
        static::$_debug = $debug;
    }

    protected static function _log(string $type, mixed $message): void
    {
        if ($message instanceof Throwable) {
            $message = $message->__toString();
        } elseif (is_object($message)) {
            $message = serialize($message);
        } elseif (is_array($message)) {
            $message = StringUtil::jsonEncode($message);
        }

        $file = '';
        $line = '';
        if (static::$_debug) {
            $array = debug_backtrace();
            $file = basename($array[1]['file']);
            $line = $array[1]['line'];
        }
        $trace = "$file:$line";

        switch ($type) {
            case 'warn':
                static::$_debug && printf("%s \033[33m\033[1m[WARN] \033[0m %s %s\n", date('Y-m-d H:i:s'), $trace, $message);
                $log = sprintf("%s [WARN] %s %s\n", date('Y-m-d H:i:s'), $trace, $message);
                break;
            case 'error':
                static::$_debug && printf("%s \033[31m\033[1m[ERROR]\033[0m %s %s\n", date('Y-m-d H:i:s'), $trace, $message);
                $log = sprintf("%s [ERROR] %s %s\n", date('Y-m-d H:i:s'), $trace, $message);
                break;
            default:
                static::$_debug && printf("%s \033[36m\033[1m[INFO] \033[0m %s %s\n", date('Y-m-d H:i:s'), $trace, $message);
                $log = sprintf("%s [INFO] %s %s\n", date('Y-m-d H:i:s'), $trace, $message);
        }

        $logFile = static::$_log_dir . date('Y-m-d') . '.log';
        file_put_contents($logFile, $log, FILE_APPEND | LOCK_EX);
    }
}

Logger::init();

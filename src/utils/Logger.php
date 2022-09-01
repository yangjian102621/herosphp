<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\utils;

use herosphp\exception\HeroException;

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
        static::$_log_dir = RUNTIME_PATH . 'logs/';
        if (!file_exists(static::$_log_dir)) {
            FileUtil::makeFileDirs(static::$_log_dir);
        }

        if (get_app_config('debug')) {
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

    private static function _log(string $type, mixed $message): void
    {
        if ($message instanceof HeroException) {
            $message = $message->toString();
        } elseif (is_object($message)) {
            $message = serialize($message);
        } elseif (is_array($message)) {
            $message = StringUtil::jsonEncode($message);
        }

        $log = '';
        $file = '';
        if (static::$_debug) {
            $array = debug_backtrace();
            $file = basename($array[1]['file']);
        }

        switch ($type) {
            case 'warn':
                printf("%s \033[33m\033[1m[WARN] \033[0m %s %s\n", date('Y-m-d H:i:s'), $file, $message);
                $log = sprintf("%s [WARN] %s %s\n", date('Y-m-d H:i:s'), $file, $message);
                break;
            case 'error':
                printf("%s \033[31m\033[1m[ERROR]\033[0m %s %s\n", date('Y-m-d H:i:s'), $file, $message);
                $log = sprintf("%s [ERROR] %s %s\n", date('Y-m-d H:i:s'), $file, $message);
                break;
            default:
                printf("%s \033[36m\033[1m[INFO] \033[0m %s %s\n", date('Y-m-d H:i:s'), $file, $message);
                $log = sprintf("%s [INFO] %s %s\n", date('Y-m-d H:i:s'), $file, $message);
        }

        $log_file = static::$_log_dir . date('Y-m-d') . '.log';
        file_put_contents($log_file, $log, FILE_APPEND | LOCK_EX);
    }
}

Logger::init();

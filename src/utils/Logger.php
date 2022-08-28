<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\utils;

use herosphp\exception\HeroException;
use herosphp\utils\FileUtils;

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
            FileUtils::makeFileDirs(static::$_log_dir);
        }

        if (get_app_config('debug')) {
            static::$_debug = true;
        }
    }

    public static function info($message): void
    {
        if (is_object($message)) {
            $message = serialize($message);
        } elseif (is_array($message)) {
            $message = json_encode($message);
        }

        printf(date('Y-m-d H:i:s') . " \033[36m\033[1m[INFO] \033[0m {$message} \n");

        if (static::$_debug) {
            $log_file = static::$_log_dir . date('Y-m-d') . 'log';
            file_put_contents($log_file, date('Y-m-d H:i:s') . ' [INFO]  ' . $message . "\n", FILE_APPEND | LOCK_EX);
        }
    }

    public static function warn($message): void
    {
        if (is_object($message)) {
            $message = serialize($message);
        } elseif (is_array($message)) {
            $message = json_encode($message);
        }

        printf(date('Y-m-d H:i:s') . " \033[33m\033[1m[WARN] \033[0m {$message} \n");

        if (static::$_debug) {
            if (static::$_debug) {
                $log_file = static::$_log_dir . date('Y-m-d') . 'log';
                file_put_contents($log_file, date('Y-m-d H:i:s') . ' [WARN] ' . $message . "\n", FILE_APPEND | LOCK_EX);
            }
        }
    }

    public static function error($message): void
    {
        if ($message instanceof HeroException) {
            $message = $message->toString();
        } elseif (is_object($message)) {
            $message = serialize($message);
        } elseif (is_array($message)) {
            $message = json_encode($message);
        }

        printf(date('Y-m-d H:i:s') . " \033[31m\033[1m[ERROR]\033[0m {$message} \n");

        if (static::$_debug) {
            $log_file = static::$_log_dir . date('Y-m-d') . 'log';
            file_put_contents($log_file, date('Y-m-d H:i:s') . ' [ERROR] ' . $message . "\n", FILE_APPEND | LOCK_EX);
        }
    }

    // enable|disabled debug mode
    public static function debug($debug): void
    {
        static::$_debug = $debug;
    }
}

Logger::init();

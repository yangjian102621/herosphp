<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\utils;

use herosphp\exception\HeroException;
use herosphp\files\FileUtils;

/**
 * 日志工具类
 * ---------------------------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class Logger
{
    protected static string $_log_dir;

    public static function init(): void
    {
        static::$_log_dir = RUNTIME_PATH . 'logs/';
        if (!file_exists(static::$_log_dir)) {
            FileUtils::makeFileDirs(static::$_log_dir);
        }
    }

    public static function info($message): bool
    {
        if (is_object($message)) {
            $message = serialize($message);
        } elseif (is_array($message)) {
            $message = json_encode($message);
        }

        $log_file = static::$_log_dir . date('Y-m-d') . 'log';
        return file_put_contents($log_file, '[' . date('Y-m-d H:i:s') . '] [INFO] ' . $message . "\n", FILE_APPEND);
    }

    public static function warn($message): bool
    {
        if (is_object($message)) {
            $message = serialize($message);
        } elseif (is_array($message)) {
            $message = json_encode($message);
        }

        $log_file = static::$_log_dir . date('Y-m-d') . 'log';
        return file_put_contents($log_file, '[' . date('Y-m-d H:i:s') . '] [WARN] ' . $message . "\n", FILE_APPEND);
    }

    public static function error($message): bool
    {
        if ($message instanceof HeroException) {
            $message = $message->toString();
        } elseif (is_object($message)) {
            $message = serialize($message);
        } elseif (is_array($message)) {
            $message = json_encode($message);
        }

        $log_file = static::$_log_dir . date('Y-m-d') . 'log';
        return file_put_contents($log_file, '[' . date('Y-m-d H:i:s') . '] [ERROR] ' . $message . "\n", FILE_APPEND);
    }
}

Logger::init();

<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\core;

/**
 * Config file parser tool class
 * ----------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class Config
{
    // config data
    private static array $_data = [];

    private static string $_extension = '.config.php';

    // get configs with specified config filename
    public static function get(string $name, string $key = null, string $default = null): mixed
    {
        if (!isset(static::$_data[$name])) {
            $file = CONFIG_PATH . $name . static::$_extension;
            if (file_exists($file)) {
                static::$_data[$name] = include $file;
            } else {
                static::$_data[$name] = [];
            }
        }

        // return all configs
        if ($key === null) {
            return static::$_data[$name];
        }

        // return the specified configs
        if (str_contains($key, '.')) {
            $keys = explode('.', $key);
            $value = static::$_data[$name];
            foreach ($keys as $index) {
                if (!isset($value[$index])) {
                    return $default;
                }
                $value = $value[$index];
            }
            return $value;
        } else {
            return static::$_data[$name][$key] ?? $default;
        }
    }

    public static function set(string $name, string $key = null, mixed $value)
    {
        if (!isset(static::$_data[$name]) || $key == null) {
            static::$_data[$name] = $value;
        } else {
            static::$_data[$name][$key] = $value;
        }
    }
}

<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

/**
 * 框架公共的常用的全局函数
 * ---------------------------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */

use herosphp\core\Config;
use herosphp\exception\HeroException;

// get var_dump output and return it as a string
if (! function_exists('var_export_all')) {
    function var_export_all(): string
    {
        $_args = func_get_args();
        if (count($_args) === 0) {
            return '';
        }

        $output = [];
        foreach ($_args as $val) {
            $output[] = print_r($val, true);
        }

        return implode(',', $output);
    }
}
// 终端高亮打印青色
if (! function_exists('print_info')) {
    function print_info($message)
    {
        printf("\033[36m\033[1m{$message}\033[0m\n");
    }
}

// 终端高亮打印绿色
if (! function_exists('print_success')) {
    function print_success($message)
    {
        printf("\033[32m\033[1m{$message}\033[0m\n");
    }
}

// 终端高亮打印红色
if (! function_exists('print_error')) {
    function print_error($message)
    {
        printf("\033[31m\033[1m{$message}\033[0m\n");
    }
}

// 终端高亮打印黄色
if (! function_exists('print_warning')) {
    function print_warning($message)
    {
        printf("\033[33m\033[1m{$message}\033[0m\n");
    }
}

// throw new exception
if (! function_exists('E')) {
    function E($message)
    {
        throw new HeroException($message);
    }
}

// get current time
if (! function_exists('timer')) {
    function timer(): float
    {
        [$msec, $sec] = explode(' ', microtime());
        return ((float)$msec + (float)$sec);
    }
}

// get app config
if (! function_exists('get_app_config')) {
    function get_app_config($key)
    {
        return Config::getValue('app', $key);
    }
}

// get cmd args
if (! function_exists('get_cli_args')) {
    function get_cli_args($argv): array
    {
        $params = ['__file__' => array_shift($argv), '__url__' => array_shift($argv)];
        foreach ($argv as $v) {
            if (strlen($v) < 3 || strncmp($v, '--', 2) != 0) {
                continue;
            }

            if (preg_match('/--([a-zA-Z0-9]+)=([^\s]+)/', $v, $m) == 0) {
                continue;
            }
            $params[$m[1]] = $m[2];
        }
        return $params;
    }
}

//env_config
if (! function_exists('env_config')) {
    if (!function_exists('env_config')) {
        function env_config(string $key, $default = null)
        {
            global $env;

            return $env->get($key, $default);
        }
    }
}

//便捷写法
if (!function_exists('config')) {
    function config(string $key, $default = null):mixed
    {
        return Config::getValueByPoint($key, $default);
    }
}

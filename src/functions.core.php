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
 * @since v1.2.1
 */

use herosphp\core\Config;
use herosphp\exception\HeroException;

// get var_dump output and return it as a string
if (!function_exists('var_dump_r')) {
    function var_dump_r(): string
    {
        $_args = func_get_args();
        if (count($_args) === 0) {
            return '';
        }

        ob_start();
        var_dump($_args);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}

// 终端高亮打印绿色
if (!function_exists('printSuccess')) {
    function printSuccess($message)
    {
        printf("\033[32m\033[1m{$message}\033[0m\n");
    }
}


// 终端高亮打印红色
if (!function_exists('printError')) {
    function printError($message)
    {
        printf("\033[31m\033[1m{$message}\033[0m\n");
    }
}

// 终端高亮打印黄色
if (!function_exists('printWarning')) {
    function printWarning($message)
    {
        printf("\033[33m\033[1m{$message}\033[0m\n");
    }
}

// throw new exception
if (!function_exists('E')) {
    function E($message)
    {
        throw new HeroException($message);
    }
}

// get current time
if (!function_exists('timer')) {
    function timer()
    {
        list($msec, $sec) = explode(' ', microtime());
        return ((float)$msec + (float)$sec);
    }
}

// get app config
if (!function_exists('getAppConfig')) {
    function getAppConfig($key)
    {
        return Config::getValue('app', $key);
    }
}

// get cmd args
if (!function_exists('getCliArgs')) {
    function getCliArgs($argv)
    {
        $params = array('__file__' => array_shift($argv), '__url__' => array_shift($argv));
        foreach ($argv as $v) {
            if (strlen($v) < 3 || strncmp($v, "--", 2) != 0) {
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

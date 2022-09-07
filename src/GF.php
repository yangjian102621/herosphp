<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp;

/**
 * 框架公共的常用的全局函数
 * ---------------------------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */

use herosphp\core\BeanContainer;
use herosphp\core\Config;
use herosphp\utils\ModelTransformUtils;
use Workerman\Worker;

class GF
{
    // get var_dump output and return it as a string
    public static function exportVar(): string
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

    // 终端高亮打印青色
    public static function printInfo(string $message)
    {
        printf("\033[36m\033[1m%s\033[0m\n", $message);
    }

    // 终端高亮打印绿色
    public static function printSuccess(string $message)
    {
        printf("\033[32m\033[1m%s\033[0m\n", $message);
    }

    // 终端高亮打印红色
    public static function printError(string $message)
    {
        printf("\033[31m\033[1m%s\033[0m\n", $message);
    }

    // 终端高亮打印黄色
    public static function printWarning(string $message)
    {
        printf("\033[33m\033[1m%s\033[0m\n", $message);
    }

    // get current time
    public static function timer(): float
    {
        [$msec, $sec] = explode(' ', microtime());
        return ((float)$msec + (float)$sec);
    }

    // get app config
    public static function getAppConfig(string $key)
    {
        return Config::get('app', $key);
    }

    // process run
    /** @noinspection PhpObjectFieldsAreOnlyWrittenInspection */
    public static function processRun(string $processName, array $config = []): void
    {
        $listen = $config['listen'] ?? null;
        $content = $config['content'] ?? [];
        $worker = new Worker($listen, $content);
        $propertyMap = [
            'count',
            'user',
            'group',
            'reloadable',
            'reusePort',
            'transport',
            'protocol',
        ];
        $worker->name = $processName;
        foreach ($propertyMap as $property) {
            if (isset($config[$property])) {
                $worker->$property = $config[$property];
            }
        }
        $worker->onWorkerStart = function ($worker) use ($config) {
            if (isset($config['handler'])) {
                if (!class_exists($config['handler'])) {
                    echo "process error: class {$config['handler']} not exists\r\n";
                    return;
                }
                $instance = BeanContainer::make($config['handler'], $config['constructor'] ?? []);
                static::workerBind($worker, $instance);
            }
        };
    }

    // process bind callback
    public static function workerBind(Worker $worker, $class)
    {
        $callbackMap = [
            'onConnect',
            'onMessage',
            'onClose',
            'onError',
            'onBufferFull',
            'onBufferDrain',
            'onWorkerStop',
            'onWebSocketConnect',
        ];
        foreach ($callbackMap as $name) {
            if (method_exists($class, $name)) {
                $worker->$name = [$class, $name];
            }
        }
        if (method_exists($class, 'onWorkerStart')) {
            call_user_func([$class, 'onWorkerStart'], $worker);
        }
    }

    /**
     * 转Vo
     * @param string $class
     * @return object
     */
    public static function params2vo(string $class):object
    {
        return ModelTransformUtils::map2model($class, [...WebApp::$_request->get(),...WebApp::$_request->post()]);
    }

    /**
     * middlewares pipeline
     */
    public static function pipeline(array $classes, callable $initial): callable
    {
        return array_reduce(array_reverse($classes), function ($res, $currClass) {
            return function ($request) use ($res, $currClass) {
                return (new $currClass())->process($request, $res);
            };
        }, $initial);
    }
}

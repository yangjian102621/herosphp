<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp;

use FastRoute\Dispatcher;
use herosphp\annotation\AnnotationParser;
use herosphp\core\Config;
use herosphp\core\HttpRequest;
use herosphp\core\HttpResponse;
use herosphp\core\Router;
use herosphp\exception\HeroException;
use herosphp\utils\Logger;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http;
use Workerman\Worker;

/**
 * WebApp main program
 *
 * @author RockYang<yangjian102621@gmail.com>
 */
class WebApp
{
    protected static Dispatcher $_dispatcher;

    // app config
    private static array $_config = [
        'listen' => 'http://0.0.0.0:2345',
        'context' => [],
        'worker_count' => 4,
        'reloadable' => 'true',
    ];

    public static function run(): void
    {
        // loading app configs
        $config = Config::getValue('app', 'server');
        if (!empty($config)) {
            static::$_config = array_merge(static::$_config, $config);
        }

        static::startServer();
    }

    public static function startServer()
    {
        // create a http worker
        $worker = new Worker(static::$_config['listen'], static::$_config['context']);
        $worker->count = static::$_config['worker_count'];
        $worker->reloadable = static::$_config['reloadable'];

        $worker->onWorkerStart = function ($w) {
            static::onWorkerStart($w);
        };

        Http::requestClass(HttpRequest::class);
        $worker->onMessage = function (TcpConnection $connection, HttpRequest $request) {
            static::onMessage($connection, $request);
        };
    }

    public static function onWorkerStart(Worker $worker)
    {
        // scan the class file and init the router info
        AnnotationParser::run(APP_PATH, 'app\\');

        static::$_dispatcher = Router::getDispatcher();
    }

    public static function onMessage(TcpConnection $connection, HttpRequest $request)
    {
        try {
            $routeInfo = static::$_dispatcher->dispatch($request->method(), $request->path());
            switch ($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                    $file = static::getPublicFile($request->path());
                    if ($file === '') {
                        $connection->send(static::response(404, 'Page not found.'));
                    } else {
                        $connection->send((new HttpResponse())->withFile($file));
                    }
                    break;
                case Dispatcher::METHOD_NOT_ALLOWED:
                    $connection->send(static::response(405, 'Method not allowed.'));
                    break;
                case Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];
                    // 注入 HttpRequest 参数
                    if (in_array(HttpRequest::class, $handler['params'])) {
                        array_unshift($vars, $request);
                    }

                    $res = call_user_func_array([$handler['obj'], $handler['method']], $vars);
                    $connection->send($res);
                    break;
                default:
                    E("router parse error for {$request->path()}");
            }
        } catch (HeroException $e) {
            $connection->send(static::response(500, 'Oops, it seems something went wrong.'));
            if (get_app_Config('debug')) {
                Logger::error($e->toString());
            }
        }
    }

    // create a new HttpResponse
    public static function response(int $code, string $body): HttpResponse
    {
        return new HttpResponse(status: $code, body: $body);
    }

    // get the path for public static files
    public static function getPublicFile(string $path): string
    {
        $file = PUBLIC_PATH . $path;
        if (file_exists($file)) {
            return $file;
        }
        return '';
    }
}

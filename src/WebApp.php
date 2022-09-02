<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp;

use app\exception\ExceptionHandler;
use FastRoute\Dispatcher;
use herosphp\annotation\AnnotationParser;
use herosphp\core\Config;
use herosphp\core\HttpRequest;
use herosphp\core\HttpResponse;
use herosphp\core\Router;
use herosphp\exception\RouterException;
use herosphp\utils\Logger;
use Throwable;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http;
use Workerman\Worker;

define('X_POWER', 'Herosphp/4.0.0'); // define framework version

/**
 * WebApp main program
 *
 * @author RockYang<yangjian102621@gmail.com>
 */
class WebApp
{
    public static HttpRequest $_request;

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
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            GF::printWarning('require PHP > 8.1.0 !');
            exit();
        }

        // loading app configs
        $config = Config::get('app');
        if (!empty($config)) {
            static::$_config = array_merge(static::$_config, $config['server']);
        }

        // set timezone
        date_default_timezone_set($config['timezone']);
        // set error report level
        error_reporting($config['error_reporting']);

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
            static::$_request = $request;
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
                    // inject request object
                    // @Note: 路径参数的相对位置应该和方法参数的相对位置保持一致
                    // 1. Controller methods can have no parameters.
                    // 2. The first parameter must be HttpRequest object.
                    // 3. Method parameters should keep the same order of the route path vars
                    if (in_array(HttpRequest::class, $handler['params'])) {
                        array_unshift($vars, $request);
                    }

                    $res = call_user_func_array([$handler['obj'], $handler['method']], $vars);
                    $connection->send($res);
                    break;
                default:
                    throw new RouterException("router parse error for {$request->path()}");
            }

            // catch and handle the exception
        } catch (Throwable $e) {
            $connection->send(static::exceptionResponse($e, $request));
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

    /**
     * 统一异常处理
     * @param Throwable $e
     * @param HttpRequest $request
     * @return HttpResponse
     */
    protected static function exceptionResponse(Throwable $e, HttpRequest $request): HttpResponse
    {
        try {
            //todo: $exceptionHandler instance from container
            $exceptionHandler = new ExceptionHandler();
            $exceptionHandler->report($e);
            return $exceptionHandler->render($request, $e);
        } catch (Throwable $e) {
            if (GF::getAppConfig('debug')) {
                Logger::error($e->getMessage());
            }
            return static::response(500, 'Oops, it seems something went wrong.');
        }
    }
}

<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp;

use app\controller\UserController;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use herosphp\annotation\AnnotationParser;
use herosphp\core\HttpRequest;
use herosphp\core\Config;
use herosphp\core\Router;
use herosphp\utils\Logger;
use RuntimeException;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http;
use Workerman\Protocols\Http\Response;
use Workerman\Worker;

use function FastRoute\simpleDispatcher;

/**
 * WebApp main program
 * 
 * @author RockYang<yangjian102621@gmail.com>
 */
class WebApp
{
  // worker instance for workerman
  private static Worker $_worker;

  // cache for routers
  private static array $_routers;

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
          $connection->send(static::response(404, 'Page not found.'));
          // TODO: consider access the public resources
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

          $res = call_user_func_array(array($handler['obj'], $handler['method']), $vars);
          $connection->send($res);
          break;
        default:
          E("router parse error for {$request->path()}");
      }
    } catch (RuntimeException $e) {
      $connection->send(static::response(500, 'Oops, it seems something went wrong.'));
      if (getAppConfig('log') === true) {
        Logger::error($e);
      }
    }
  }

  // create a new Response
  public static function response(int $code, string $body): Response
  {
    return new Response(status: $code, body: $body);
  }

  // get the path for public static files
  public static function getPublicFile(HttpRequest $request): string
  {
    return '';
  }
}

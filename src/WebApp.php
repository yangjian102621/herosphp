<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp;

use app\controller\UserController;
use herosphp\core\HttpRequest;
use herosphp\core\Config;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http;
use Workerman\Worker;

/**
 * WebApp main program
 * 
 * @author RockYang<yangjian102621@163.com>
 */
class WebApp
{
  // worker instance for workerman
  private static Worker $_worker;

  // cache for routers
  private static array $_routers;

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
    $config = Config::get('app');
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
  }

  public static function onMessage(TcpConnection $connection, HttpRequest $request)
  {
    $user = new UserController();
    $user->index($request);
    $connection->send('hello world.');
  }
}

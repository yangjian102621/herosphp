<?php

// redis session configs

use Workerman\Protocols\Http\Session\FileSessionHandler;

return [
  'session_name' => 'heros-sess-token',
  'lifetime' => 1800,
  'max_clients' => 2,
  // set Cookie valid, default for current domain
  'domain' => '',
  'secure' => false,
  'http_only' => true,
  // sessionId encrypt private key
  'private_key' => 'R8ZvYP1kIR5X',

  // session handler config
  'handler_class' => FileSessionHandler::class,
  'handler_config' => null,
  // 'handler_config' => [
  //   'host' => '127.0.0.1', // Required
  //   'port' => 6379,        // Required
  //   'timeout' => 2,           // Optional
  //   'auth' => '',          // Optional
  //   'database' => 1,           // Optional
  //   'prefix' => '_session_'  // Optional
  // ],

];

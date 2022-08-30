<?php

// redis session configs

return [
  'session_name' => 'heros-sess-token',
  'lifetime' => 1800,
  'max_clients' => 2,

  // server configs
  'server' => array(
    'host'     => '127.0.0.1', // 必选参数
    'port'     => 6379,        // 必选参数
    'timeout'  => 2,           // 可选参数
    'auth'     => '******',    // 可选参数
    'database' => 1,           // 可选参数
    'prefix'   => '_session_'   // 可选参数
  ),

  'machine_id' => 0x01 // machine id for generate UUID
];

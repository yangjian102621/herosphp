<?php

// WebApp configs

return [
  'debug' => true,
  'error_reporting' => E_ALL,
  'timezone' => 'Asia/Shanghai',

  'template' => ['rules' => [], 'skin' => 'default'],

  'log_path' => '/tmp/logs/',

  // server configs
  'server' => [
    'listen' => 'http://0.0.0.0:2345',
    'context' => [],
    'worker_count' => 1,
    'reloadable' => 'true',
  ],

  'machine_id' => 0x01 // machine id for generate UUID
];

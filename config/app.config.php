<?php

// WebApp configs

return [
  'debug' => true,
  'log' => true, // whether to add logs for E() functions
  'error_reporting' => E_ALL,
  'default_timezone' => 'Asia/Shanghai',

  'template' => ['rules' => [], 'skin' => 'default'],

  // server configs
  'server' => array(
    'listen' => 'http://0.0.0.0:2345',
    'context' => [],
    'worker_count' => 1,
    'reloadable' => 'true',
  ),
];

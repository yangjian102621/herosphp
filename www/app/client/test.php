<?php
namespace client;

use herosphp\core\Loader;
use tasks\TestTask;

Loader::import('tasks.TestTask', IMPORT_CLIENT);
$task = new TestTask();
$task->run();
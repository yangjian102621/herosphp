<?php
namespace client;

use herosphp\core\Loader;
use tasks\ModelUpdateTask;

Loader::import('tasks.ModelUpdateTask', IMPORT_CLIENT);
$task = new ModelUpdateTask();
$task->run();
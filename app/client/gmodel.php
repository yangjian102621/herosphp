<?php
namespace client;

use herosphp\core\Loader;
use tasks\GmodelTask;

Loader::import('tasks.GmodelTask', IMPORT_CLIENT);
$task = new GmodelTask();
$task->run();
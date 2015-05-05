<?php

\herosphp\core\Loader::import('tasks.TestTask', IMPORT_CLIENT);
$task = new \tasks\TestTask();
$task->run();
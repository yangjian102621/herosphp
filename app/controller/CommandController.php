<?php

declare(strict_types=1);

namespace app\controller;

use herosphp\annotation\Controller;
use herosphp\core\CliBaseController;

#[Controller(CliBaseController::class)]
class CommandController extends CliBaseController
{
    protected function __init()
    {
        var_dump('Controller initialized.');
    }
}

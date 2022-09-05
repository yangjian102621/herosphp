<?php

declare(strict_types=1);

namespace app\controller;

use herosphp\annotation\Command;
use herosphp\annotation\Controller;
use herosphp\core\CliBaseController;

#[Controller(CommandController::class)]
class CommandController extends CliBaseController
{
    #[Command(uri: '/cli/test')]
    public function test()
    {
    }
}

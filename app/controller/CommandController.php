<?php

declare(strict_types=1);

namespace app\controller;

use herosphp\annotation\Command;
use herosphp\annotation\Controller;
use herosphp\core\CliBaseController;
use herosphp\core\Input;

#[Controller(CommandController::class)]
class CommandController extends CliBaseController
{
    #[Command(uri: '/cli/test')]
    public function test(Input $input)
    {
        var_dump($input->getAll());
    }
}

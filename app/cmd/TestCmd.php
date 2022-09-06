<?php

declare(strict_types=1);

namespace app\controller;

use herosphp\annotation\Action;
use herosphp\annotation\Command;
use herosphp\core\BaseCommand;
use herosphp\core\Input;

#[Command(TestCmd::class)]
class TestCmd extends BaseCommand
{
    #[Action(uri: '/cli/test')]
    public function test(Input $input)
    {
        var_dump($input->getAll());
    }
}

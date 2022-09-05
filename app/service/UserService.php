<?php

declare(strict_types=1);

namespace app\service;

use herosphp\annotation\Inject;
use herosphp\annotation\Service;
use herosphp\utils\Logger;

#[Service(UserService::class)]
class UserService
{
    #[Inject(Message::class)]
    protected Message $message;

    public function login()
    {
        Logger::info('Invoked UserService::login()');
    }
}

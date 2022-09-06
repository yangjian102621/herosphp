<?php
declare(strict_types=1);
namespace app\controller;

use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosRQueue\Client;

#[Controller(name: QueueController::class)]
class QueueController
{
    #[Get(uri: '/queue/demo', desc: '测试')]
    public function demo(): string
    {
        Client::send('demo', [1,2,3]);
        return 'ok';
    }

    #[Get(uri: '/queue/delay', desc: '测试')]
    public function delay(): string
    {
        Client::send('demo', [1,2,3,4], 5);
        return 'ok';
    }
}

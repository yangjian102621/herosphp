<?php

declare(strict_types=1);

namespace app\controller;

use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosphp\core\BaseController;
use herosphp\core\HttpRequest;
use herosphp\GF;
use herosphp\utils\HttpUtil;
use herosphp\utils\Logger;

#[Controller(TestController::class)]
class TestController extends BaseController
{
    public function __init()
    {
        var_dump('Controller initialized.');
    }

    #[Get(uri: '/test/http')]
    public function http()
    {
        return HttpUtil::init()->get('https://filfox.info/api/v1/stats/base-fee?duration=24h&samples=48');
    }

    #[Get(uri: '/test/session')]
    public function session(HttpRequest $request)
    {
        $session = $request->session(123);
        if ($session === null) {
            return GF::exportVar($request->getSessionErrNo()->getName());
        }
        $session->set('name', 'value');
        return $this->json($session->getAllClients());
    }

    #[Get(uri: '/test/log')]
    public function log()
    {
        Logger::info('This is a info log');
        Logger::warn('This is a warn log');
        Logger::error('This is a error log');

        return 'Logger test, please pay attension to console output.';
    }

    #[Get(uri: '/test/temp')]
    public function temp(HttpRequest $request)
    {
        return GF::exportVar($request->connection->getRemoteIp());
    }
}

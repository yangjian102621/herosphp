<?php

declare(strict_types=1);

namespace app\controller;

use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosphp\core\BaseController;
use herosphp\core\HttpRequest;
use herosphp\core\HttpResponse;
use herosphp\utils\StringUtil;
use herosphp\utils\HttpUtil;

#[Controller(TestController::class)]
class TestController extends BaseController
{

    #[Get(uri: '/test/http')]
    public function http()
    {
        return HttpUtil::init()->get('https://filfox.info/api/v1/stats/base-fee?duration=24h&samples=48');
    }

    #[Get(uri: '/test/session')]
    public function session(HttpRequest $request)
    {
        $session = $request->session();
        $session->set('name', 'value');
        return 'Session test';
    }
}

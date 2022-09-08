<?php

declare(strict_types=1);

namespace app\controller;

use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosphp\core\BaseController;
use herosphp\core\HttpRequest;

#[Controller(name: MiddlewareController::class)]
class MiddlewareController extends BaseController
{
    public array $middlewares = [
        ControllerMiddleware::class,
    ];

    #[Get(uri: '/midd/demo', desc: '')]
    public function demo(HttpRequest $request): string
    {
        $user = $request->getMidData(ControllerMiddleware::LOGIN_USER);
        return $user->getId();
    }
}

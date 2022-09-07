<?php
declare(strict_types=1);
namespace app\controller;

use app\model\LoginUser;
use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosphp\core\BaseController;

#[Controller(name: MiddlewareController::class)]
class MiddlewareController extends BaseController
{
    public array $middlewares = [
        ControllerMiddleware::class,
    ];

    #[Get(uri: '/midd/demo', desc: '')]
    public function demo(LoginUser $loginUser): string
    {
        return $loginUser->getId();
    }
}

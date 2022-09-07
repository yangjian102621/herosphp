<?php
declare(strict_types=1);
namespace app\controller;

use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosphp\annotation\Validate;
use herosphp\core\BaseController;
use herosphp\core\HttpRequest;
use herosphp\GF;

#[Controller(name: ValidateController::class)]
class ValidateController extends BaseController
{
    #[Get(uri: '/valid/add')]
    public function add(HttpRequest $request): string
    {
        (new UserValidate())->scene('add')->check($request->get());
        return 'ok';
    }

    #[Validate(class: UserValidate::class, scene: 'add')]
    #[Get(uri: '/valid/add2')]
    public function annotation(HttpRequest $request): string
    {
        return 'ok';
    }

    #[Validate(class: UserValidate::class, scene: 'add')]
    #[Get(uri: '/valid/vo')]
    public function vo(): string
    {
        /** @var UserVo $userVo*/
        $userVo = GF::params2vo(UserVo::class);
        return $userVo->getName().'-'.$userVo->getAge();
    }
}

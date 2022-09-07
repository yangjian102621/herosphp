<?php

namespace app\controller;

use app\model\LoginUser;
use herosphp\core\HttpRequest;
use herosphp\core\MiddlewareInterface;
use herosphp\GF;

class ControllerMiddleware implements MiddlewareInterface
{
    public function process(HttpRequest $request, callable $handler)
    {
        GF::printInfo('Controller START');

        //do auth,from token or session
        $userVo = new LoginUser();
        $userVo->setId('zhangsan');
        //end
        $request->putVARS($userVo);

        $res = $handler($request);
        GF::printInfo('Controller END');
        return $res;
    }
}

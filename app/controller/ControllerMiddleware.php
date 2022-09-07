<?php

namespace app\controller;

use herosphp\core\HttpRequest;
use herosphp\core\MiddlewareInterface;
use herosphp\GF;

class ControllerMiddleware implements MiddlewareInterface
{
    public function process(HttpRequest $request, callable $handler)
    {
        GF::printInfo('Controller START');
        $res = $handler($request);
        GF::printInfo('Controller END');
        return $res;
    }
}

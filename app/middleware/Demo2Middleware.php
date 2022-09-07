<?php
declare(strict_types=1);

namespace app\middleware;

use herosphp\core\HttpRequest;
use herosphp\core\MiddlewareInterface;
use herosphp\GF;

class Demo2Middleware implements MiddlewareInterface
{
    public function process(HttpRequest $request, callable $handler)
    {
        GF::printInfo('DEMO2 START');
        $res = $handler($request);
        GF::printInfo('DEMO2 END');
        return $res;
    }
}

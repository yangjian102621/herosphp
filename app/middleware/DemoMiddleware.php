<?php
declare(strict_types=1);

namespace app\middleware;

use herosphp\core\HttpRequest;
use herosphp\core\MiddlewareInterface;
use herosphp\GF;

class DemoMiddleware implements MiddlewareInterface
{
    public function process(HttpRequest $request, callable $handler)
    {
        GF::printInfo('DEMO START');
        $res = $handler($request);
        GF::printInfo('DEMO END');
        return $res;
    }
}

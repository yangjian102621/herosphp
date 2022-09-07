<?php
declare(strict_types=1);

use app\middleware\Demo2Middleware;
use app\middleware\DemoMiddleware;

return [
    DemoMiddleware::class,
    Demo2Middleware::class,
];

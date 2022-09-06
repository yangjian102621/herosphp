<?php
declare(strict_types=1);

namespace app\exception;

use herosphp\annotation\Component;
use herosphp\core\HttpRequest;
use herosphp\core\HttpResponse;
use herosphp\exception\BaseExceptionHandler;
use Throwable;

#[Component(name: '异常处理')]
class ExceptionHandler extends BaseExceptionHandler
{
    protected array $dontReport = [
        AuthenticationException::class,
    ];

    /**
     * 统一异常处理
     * @param HttpRequest $request
     * @param Throwable $e
     * @return HttpResponse
     */
    public function render(HttpRequest $request, Throwable $e): HttpResponse
    {
        return match (get_class($e)) {
            AuthenticationException::class => new HttpResponse(status: 200, body: 'auth'),
            default => new HttpResponse(status: 500, body: $e->getMessage()),
        };
    }
}

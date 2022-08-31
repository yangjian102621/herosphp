<?php

namespace herosphp\exception;

use herosphp\core\HttpRequest;
use herosphp\core\HttpResponse;
use herosphp\utils\Logger;
use herosphp\utils\StringUtil;
use Throwable;

/**
 * 异常统一处理
 */
abstract class BaseExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * @var array
     */
    protected array $dontReport = [];

    public function report(Throwable $e): void
    {
        if ($this->shouldntReport($e)) {
            return;
        }
        Logger::error((string)$e);
    }

    /**
     * @param HttpRequest $request
     * @param Throwable $e
     * @return HttpResponse
     */
    public function render(HttpRequest $request, Throwable $e): HttpResponse
    {
        $_debug = get_app_config('debug');
        $code = $e->getCode();
        if ($request->expectsJson()) {
            $json = ['code' => $code ?: 500, 'msg' => $_debug ? $e->getMessage() : 'Server internal error'];
            $_debug && $json['traces'] = (string)$e;
            return new HttpResponse(
                500,
                ['Content-Type' => 'application/json'],
                StringUtil::jsonEncode($json),
            );
        }
        return new HttpResponse(status: 500, body: 'Oops, it seems something went wrong.');
    }

    /**
     * @param Throwable $e
     * @return bool
     */
    protected function shouldNtReport(Throwable $e): bool
    {
        foreach ($this->dontReport as $type) {
            if ($e instanceof $type) {
                return true;
            }
        }
        return false;
    }
}

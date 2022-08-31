<?php

namespace herosphp\exception;

use herosphp\core\HttpRequest;
use herosphp\core\HttpResponse;
use Throwable;

interface ExceptionHandlerInterface
{
    /**
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e): void;

    /**
     * @param HttpRequest $request
     * @param Throwable $e
     * @return HttpResponse
     */
    public function render(HttpRequest $request, Throwable $e): HttpResponse;
}

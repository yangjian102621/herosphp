<?php

namespace herosphp\core;

interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     *
     * @param  HttpRequest  $request httpRequest
     * @param  callable  $handler
     */
    public function process(HttpRequest $request, callable $handler);
}

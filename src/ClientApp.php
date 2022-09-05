<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp;

use FastRoute\Dispatcher;
use herosphp\core\Input;

/**
 * Command line client main program
 *
 * @author RockYang<yangjian102621@gmail.com>
 */
class ClientApp
{
    protected static Dispatcher $_dispatcher;

    // Command request parameters
    protected static Input $_params;

    // Command request uri path
    protected static string $_uri;

    public static function run(int $argc, array $argv): void
    {
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            GF::printError('Error: Require PHP > 8.1.0 !');
            exit(0);
        }

        // run ONLY for command line
        if (RUN_CLI_MODE == false ) {
            GF::printError("Error: Access only for cli sapi.");
            exit(0);
        }

        if (extension_loaded('posix') === false) {
            GF::printError("Error: Posix extension is required to run the script.");
            exit(0);
        }

        if (extension_loaded('pcntl') === false) {
            GF::printError("Error: Pcntl extension is required to run the script.");
            exit(0);
        }

        // parse uri and query string
        if ($argc == 1) {
            GF::printError('Invalid action uri');
            exit(1);
        }

        // parse url
        static::_parseArgs($argv[1]);

        try {
            $routeInfo = static::$_dispatcher->dispatch($request->method(), $request->path());
            static::$_request = $request;
            switch ($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                    $file = static::getPublicFile($request->path());
                    if ($file === '') {
                        $connection->send(static::response(404, 'Page not found.'));
                    } else {
                        if (static::notModifiedSince($file)) {
                            $connection->send((new HttpResponse(304)));
                        } else {
                            $connection->send((new HttpResponse())->withFile($file));
                        }
                    }
                    break;
                case Dispatcher::METHOD_NOT_ALLOWED:
                    $connection->send(static::response(405, 'Method not allowed.'));
                    break;
                case Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];
                    // inject request object
                    // @Note: 路径参数的相对位置应该和方法参数的相对位置保持一致
                    // 1. Controller methods can have no parameters.
                    // 2. The first parameter must be HttpRequest object.
                    // 3. Method parameters should keep the same order of the route path vars
                    if (in_array(HttpRequest::class, $handler['params'])) {
                        array_unshift($vars, $request);
                    }

                    $res = call_user_func_array([$handler['obj'], $handler['method']], $vars);
                    $connection->send($res);
                    break;
                default:
                    throw new RouterException("router parse error for {$request->path()}");
            }

            // catch and handle the exception
        } catch (Throwable $e) {
            ->send(static::exceptionResponse($e, $request));
        }
    }



    // parse args
    protected static function _parseArgs(string $str): void
    {
        $pos = strpos($str, '?', 0);
        if ($pos === false) {
            static::$_uri = $str;
            return;
        }

        // parse uri
        static::$_uri = substr($str, 0, $pos);

        // parse parameters
        $query = substr($str, $pos + 1);
        $params = explode(':', $query);
        var_dump($params, $query);
    }
}

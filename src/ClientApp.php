<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp;

use FastRoute\Dispatcher;
use herosphp\annotation\AnnotationParser;
use herosphp\core\Input;
use herosphp\core\Router;
use herosphp\exception\RouterException;
use Throwable;

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
        if (RUN_CLI_MODE === false) {
            GF::printError('Error: Access only for cli sapi.');
            exit(0);
        }

        if (extension_loaded('posix') === false) {
            GF::printError('Error: Posix extension is required to run the script.');
            exit(0);
        }

        if (extension_loaded('pcntl') === false) {
            GF::printError('Error: Pcntl extension is required to run the script.');
            exit(0);
        }

        // parse uri and query string
        if ($argc === 1) {
            GF::printError('Invalid action uri');
            exit(1);
        }

        // parse url
        static::_parseArgs($argv[1]);

        // scan the class file and init the router info
        AnnotationParser::run(APP_PATH, 'app\\');
        // init dispatcher
        static::$_dispatcher = Router::getDispatcher();

        try {
            $path = static::$_uri;
            $routeInfo = static::$_dispatcher->dispatch('CMD', $path);
            switch ($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                    GF::printError("Action '{$path}' not found.");
                    break;
                case Dispatcher::METHOD_NOT_ALLOWED:
                    GF::printError("Method not allowed for '{$path}'.");
                    break;
                case Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    call_user_func([$handler['obj'], $handler['method']], static::$_params);
                    break;
                default:
                    throw new RouterException("router parse error for {$request->path()}");
            }
        } catch (Throwable $e) {
            GF::printError($e->getMessage());
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
        $arr = explode(':', $query);

        $params = [];
        foreach ($arr as $val) {
            if (!str_contains($val, '=')) {
                continue;
            }

            $p = explode('=', $val);
            $params[$p[0]] = $p[1];
        }

        static::$_params = new Input($params);
    }
}
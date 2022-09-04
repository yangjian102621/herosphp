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
            GF::printWarning('require PHP > 8.1.0 !');
            exit(0);
        }

        // parse uri and query string
        if ($argc == 1) {
            GF::printError('Invalid action uri');
            exit(1);
        }

        // parse url
        static::_parseArgs($argv[1]);
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

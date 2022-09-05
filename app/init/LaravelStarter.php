<?php
declare(strict_types=1);
namespace app\init;

use herosLdb\LaravelDbStarter;
use herosphp\core\Config;

/**
 * Laravel启动器
 */
class LaravelStarter extends LaravelDbStarter
{
    protected static bool $debug = true;
}
LaravelStarter::init(Config::get(name:'database', default: []));

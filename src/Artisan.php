<?php
/**
 * client comand line tool
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v2.0.0
 */

namespace herosphp;

use cli\BlogAction;
use ReflectionClass;

class Artisan
{
    public static function run($params)
    {
        //print_r($params);
        if (!isset($params['__url__'])) {
            E('[url] parameter is need.');
        }
        $urls = explode('/', $params['__url__']);
        if (count($urls) < 3) {
            E("Invalid url for {$params['__url__']}");
        }

        $method = array_pop($urls);
        $className = array_pop($urls);
        $className = ucfirst($className).'Action';
        array_push($urls, $className);
        $classPath = implode('\\', $urls);
        $clazz = new ReflectionClass("cli\\BlogAction");
        $method = $clazz->getMethod($method);
        $method->invoke($clazz->newInstance());
    }
}

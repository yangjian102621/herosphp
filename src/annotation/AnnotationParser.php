<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\annotation;

use herosphp\core\BeanContainer;
use herosphp\core\Router;
use herosphp\exception\HeroException;
use ReflectionClass;

/**
 * Annotation parser tool class
 *
 * scan class files in the specified dir, parser the annotation and extract Http-Request-Router and Injected-Beans
 *
 * @author RockYang<yangjian102621@gmail.com>
 */
class AnnotationParser
{
    protected static array $_httpMethodAny = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'HEAD', 'PATCH'];

    // Annotations that need to be parsed
    protected static array $_parseClassAnnotations = ['#[Component(', '#[Service(', '#[Bootstrap('];

    // annotation parse enter method
    /**
     * @throws \ReflectionException
     */
    public static function run(string $classDir, string $namespacePrefix): void
    {
        if (defined('RUN_WEB_MODE')) {
            static::$_parseClassAnnotations[] = '#[Controller(';
        } elseif (defined('RUN_CLI_MODE')) {
            static::$_parseClassAnnotations[] = '#[Command(';
        }

        // scan class files
        static::scanClassFiles($classDir);

        // parse annotations
        foreach (get_declared_classes() as $class) {
            if (!str_starts_with($class, $namespacePrefix)) {
                continue;
            }
            static::parseAnnotations($class);
        }
    }

    /**
     * scan class files
     *
     * @param string $classDir the dir to be scaned, must be a absolute path
     */
    public static function scanClassFiles(string $classDir): void
    {
        $handler = opendir($classDir);
        while (($filename = readdir($handler)) !== false) {
            if ($filename === '.' || $filename === '..') {
                continue;
            }

            $path = $classDir . DIRECTORY_SEPARATOR . $filename;
            if (is_dir($path)) {
                static::scanClassFiles($path);
            } elseif (is_file($path) && str_ends_with($path, '.php') && static::needParse($path)) {
                // require source class file
                require $path;
            }
        }
    }

    // do parse annotation

    /**
     * @throws \ReflectionException
     */
    public static function parseAnnotations(string $class): void
    {
        // parse route(request map) annotations
        $clazz = new ReflectionClass($class);
        $attrs = $clazz->getAttributes();
        if (empty($attrs)) {
            return;
        }

        // build instance
        BeanContainer::build($class);

        foreach ($attrs as $attr) {
            $name = $attr->getName();
            if ($name === Controller::class || $name === Command::class) {
                static::parseRouter($clazz);
            }
        }
    }

    // check if the class need to be parsed
    // we ONLY build classes with the specified Annotations
    protected static function needParse(string $classFile): bool
    {
        $handler = fopen($classFile, 'r');
        if ($handler === false) {
            throw new HeroException("failed to open class file '{$classFile}'.");
        }

        $res = false;
        while (($line = fgets($handler, 1024))) {
            if (str_starts_with($line, 'class ')) {
                break;
            }

            foreach (static::$_parseClassAnnotations as $prefix) {
                if (str_contains($line, $prefix)) {
                    $res = true;
                    break;
                }
            }
        }

        fclose($handler);
        return $res;
    }

    protected static function parseRouter(ReflectionClass $clazz): void
    {
        foreach ($clazz->getMethods() as $method) {
            if (!$method->isPublic()) {
                continue;
            }

            $attrs = $method->getAttributes();
            if (empty($attrs)) {
                continue;
            }

            $attrs = $method->getAttributes(RequestMap::class);
            $attrs = empty($attrs) ? $method->getAttributes(Post::class) : $attrs;
            $attrs = empty($attrs) ? $method->getAttributes(Get::class) : $attrs;
            $attrs = empty($attrs) ? $method->getAttributes(Action::class) : $attrs;

            if (empty($attrs)) {
                continue;
            }

            $attr = $attrs[0];
            $paramsType = [];
            foreach ($method->getParameters() as $p) {
                $t = $p->getType()?->getName();
                if ($t !== null) {
                    $paramsType[$p->getName()] = $t;
                }
            }

            $args = $attr->getArguments();
            if (!isset($args['uri'])) {
                throw new HeroException('The uri Attribute is needed.');
            }

            // 处理特殊参数
            switch ($attr->getName()) {
                case RequestMap::class:
                    if (strtoupper($args['method']) === 'ANY') {
                        $args['method'] = static::$_httpMethodAny;
                    }
                    break;
                case Post::class:
                    $args['method'] = 'POST';
                    break;
                case Get::class:
                    $args['method'] = 'GET';
                    break;
                case Action::class:
                    $args['method'] = 'CMD';
                    break;
            }

            $obj = BeanContainer::get($clazz->getName());
            $handler = ['obj' => $obj, 'method' => $method->getName(), 'params_type' => $paramsType];
            // register route
            if (is_array($args['uri'])) {
                foreach ($args['uri'] as $val) {
                    Router::add($val, $args['method'], $handler);
                }
            } else {
                Router::add($args['uri'], $args['method'], $handler);
            }
        }
    }
}

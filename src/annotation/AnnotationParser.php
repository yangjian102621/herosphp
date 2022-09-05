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
    protected static array $_http_method_any = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'HEAD', 'PATCH'];

    protected static array $_parse_class_annotations = [Component::class,Controller::class,Service::class];

    /**
     * @param string $classDir
     * @param string $namespacePrefix
     * @return void
     * @throws \ReflectionException
     */
    public static function run(string $classDir, string $namespacePrefix): void
    {
        static::scanClassFiles($classDir);

        static::parse($namespacePrefix);
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
            } elseif (is_file($path) && str_ends_with($path, '.php')) {
                // require source class file
                require_once $path;
            }
        }
    }

    /**
     * do parse annotations
     * @throws \ReflectionException
     */
    public static function parse(string $namespacePrefix): void
    {
        foreach (get_declared_classes() as $class) {
            if (!str_starts_with($class, $namespacePrefix)) {
                continue;
            }
            static::parseAnnotations($class);
        }
    }

    /**
     * parse controller annotation
     * @throws \ReflectionException
     */
    public static function parseAnnotations(string $class): void
    {
        // parse route(request map) annotations
        $clazz = new \ReflectionClass($class);

        if (!static::checkClassAnnotationExist($clazz)) {
            return;
        }
        // build instance
        BeanContainer::build($class);
        foreach ($clazz->getAttributes() as $attr) {
            if ($attr->getName() === Controller::class) {
                static::parseController($clazz);
            }
        }
    }

    /**
     * @param ReflectionClass $clazz
     * @return bool
     */
    protected static function checkClassAnnotationExist(\ReflectionClass $clazz): bool
    {
        //only build $_parse_class_annotations classes
        if ($clazz->getAttributes()) {
            foreach ($clazz->getAttributes() as $attr) {
                if (in_array($attr->getName(), static::$_parse_class_annotations)) {
                    return true;
                }
            }
        }
        return false;
    }

    protected static function parseController(ReflectionClass $clazz): void
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
            if (empty($attrs)) {
                $attrs = $method->getAttributes(Post::class);
            }
            if (empty($attrs)) {
                $attrs = $method->getAttributes(Get::class);
            }
            if (empty($attrs)) {
                continue;
            }

            $attr = $attrs[0];
            $params = [];
            foreach ($method->getParameters() as $p) {
                $t = $p->getType()?->getName();
                if ($p->getType()?->getName() !== null) {
                    $params[] = $t;
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
                        $args['method'] = static::$_http_method_any;
                    }
                    break;
                case Post::class:
                    $args['method'] = 'POST';
                    break;
                case Get::class:
                    $args['method'] = 'GET';
                    break;
            }

            $obj = BeanContainer::get($clazz->getName());
            $handler = ['obj' => $obj, 'method' => $method->getName(), 'params' => $params];
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

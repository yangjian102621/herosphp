<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\core;

use herosphp\annotation\Inject;
use herosphp\exception\HeroException;
use ReflectionClass;
use ReflectionException;

/**
 * BeanContainer class
 *
 * @author RockYang<yangjian102621@gmail.com>
 */
class BeanContainer
{
    protected static array $_instances = [];

    // get beans
    public static function get(string $name): mixed
    {
        if (isset(static::$_instances[$name])) {
            return static::$_instances[$name];
        }

        return null;
    }

    // register a new instance
    public static function register(string $name, object $value): void
    {
        if (isset(static::$_instances[$name])) {
            return;
        }

        static::$_instances[$name] = $value;
    }

    // add or update a new object
    public static function put(string $name, object $value): void
    {
        static::$_instances[$name] = $value;
    }

    // create an instance with specified constructor args
    public static function make(string $name, array $constructor = []): mixed
    {
        if (!class_exists($name)) {
            throw new HeroException("Class '$name' not found");
        }
        if (isset(static::$_instances[$name])) {
            return static::$_instances[$name];
        }
        $value = new $name(...array_values($constructor));
        static::put($name, $value);
        return $value;
    }

    /**
     * build a instance with specified class path
     * auto-inject the properties and put it to bean container.
     * @throws ReflectionException
     */
    public static function build(string $class): object
    {
        $obj = static::get($class);
        if ($obj != null) {
            return $obj;
        }

        $clazz = new ReflectionClass($class);
        $obj = $clazz->newInstance();
        // scan Inject getProperties
        foreach ($clazz->getProperties() as $property) {
            $_attrs = $property->getAttributes(Inject::class);
            if (empty($_attrs)) {
                continue;
            }

            // find property class name
            $_attr = $_attrs[0];
            $name = $property->getType()->getName();
            $_args = $_attr->getArguments();
            if (!empty($_args)) {
                $name = array_shift($_args);
            }

            // set property accessibility
            // @Note: As of PHP 8.1.0, calling this method has no effect; all properties are accessible by default.
            // $property->setAccessible(true);
            $property->setValue($obj, static::build($name));
        }

        // register object to bean pool
        static::register($clazz->getName(), $obj);
        return $obj;
    }
}

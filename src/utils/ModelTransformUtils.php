<?php

namespace herosphp\utils;

use herosphp\exception\HeroException;
use ReflectionClass;

/**
 * 模型转换工具
 * -----------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class ModelTransformUtils
{
    /**
     * convert map(key => value array) to entity object
     * @throws \ReflectionException
     */
    public static function map2model(string|object $class, array $map): object|null
    {
        if (empty($map)) {
            return null;
        }
        if (is_string($class)) { // create object
            $refClass = new ReflectionClass($class);
            $obj = $refClass->newInstance();
        } else {
            $obj = $class;
            $refClass = new ReflectionClass($obj);
        }

        foreach ($map as $key => $val) {
            $methodName = 'set' . ucwords(StringUtil::ul2hump($key));
            if ($refClass->hasMethod($methodName)) {
                $method = $refClass->getMethod($methodName);
                try {
                    $method->invoke($obj, $map[$key]);
                } catch (\Exception $e) {
                    throw new HeroException($e->getMessage());
                }
            }
        }
        return $obj;
    }

    // convert model entity to map
    public static function model2map(object $model): array
    {
        $refClass = new ReflectionClass($model);
        $properties = $refClass->getProperties();
        $map = [];
        foreach ($properties as $value) {
            $property = $value->getName();
            if (strpos($property, '_')) {
                $property = StringUtil::ul2hump($property); //转换成驼锋格式
            }
            $method = 'get' . ucfirst($property);
            $map[$property] = $model->$method();
        }
        return $map;
    }
}

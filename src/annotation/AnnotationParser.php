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

  // Annotations for router types
  protected static array $_route = [RequestMap::class, Get::class, Post::class];

  // Annotations for bean types
  // TODO: if we should put the 'Controller' instance as a Bean?
  protected static array $_bean = ['Dao', 'Service', 'Component'];

  protected static array $_http_method_any = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'HEAD', 'PATCH'];

  public static function run(string $class_dir, string $namespace_prefix): void
  {
    static::scanClassFiles($class_dir);

    static::parse($namespace_prefix);
  }

  /**
   * scan class files
   * 
   * @param $class_dir the dir to be scaned, must be a absolute path
   */
  public static function scanClassFiles(string $class_dir): void
  {
    $handler = opendir($class_dir);
    while (($filename = readdir($handler)) !== false) {
      if ($filename === '.' || $filename === '..') {
        continue;
      }

      $path = $class_dir . DIRECTORY_SEPARATOR . $filename;
      if (is_dir($path)) {
        static::scanClassFiles($path);
      } elseif (is_file($path) && str_ends_with($path, '.php')) {
        // require source class file
        require_once $path;
      }
    }
  }

  // do parse annotations
  public static function parse(string $namespace_prefix): void
  {
    foreach (get_declared_classes() as $clazz) {
      if (!str_starts_with($clazz, $namespace_prefix)) {
        continue;
      }

      $clazz = new ReflectionClass($clazz);
      $class_attr = $clazz->getAttributes();
      foreach ($class_attr as $attr) {
        switch ($attr->getName()) {
          case Controller::class: // controller
            static::parseController($clazz);
            break;
        }
      }
    }
  }

  // parse controller annotation
  public static function parseController(ReflectionClass $clazz)
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
        if ($p->getType()?->getName() !== NULL) {
          $params[] = $t;
        }
      }

      $args = $attr->getArguments();
      if (!isset($args['uri'])) {
        throw new HeroException("The uri Attribute is needed.");
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

      // get the controller instance
      $obj = BeanContainer::get($clazz->getName());
      if ($obj === null) {
        $obj = $clazz->newInstance();
        BeanContainer::register($clazz->getName(), $obj);
      }
      $handler = ['obj' => $obj, 'method' => $method->getName(), 'params' => $params];
      // register route
      Router::add($args['uri'], $args['method'], $handler);
    }
  }
}

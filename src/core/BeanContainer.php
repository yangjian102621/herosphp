<?php
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\core;

/**
 * BeanContainer class
 * 
 * @author RockYang<yangjian102621@163.com>
 */
class BeanContainer
{

  protected static array $_instances = [];

  public static function get(string $name): mixed
  {
    if (isset(static::$_instances[$name])) {
      return static::$_instances[$name];
    }

    return null;
  }

  public static function register(string $name, object $value)
  {
    static::$_instances[$name] = $value;
  }
}

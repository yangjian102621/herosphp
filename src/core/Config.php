<?php
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\core;

/**
 * 配置文档解析工具类
 * 
 * @author RockYang<yangjian102621@163.com>
 */
class Config
{
  private static array $_configs = [];
  private static string $_extension = '.config.php';

  // get configs with specified config filename
  public static function get(string $name): array
  {
    if (!isset(static::$_configs[$name])) {
      $file = CONFIG_PATH . $name . static::$_extension;
      if (file_exists($file)) {
        static::$_configs[$name] = include $file;
      } else {
        static::$_configs[$name] = [];
      }
    }

    return static::$_configs[$name];;
  }

  // get config value with specified key
  public static function getValue(string $name, string $key): mixed
  {
    $config = [];
    if (!isset(static::$_configs[$name])) {
      $config = static::get($name);
    } else {
      $config = static::$_configs[$name];
    }

    return $config[$key];
  }

  // get all configs 
  public static function getAll(): array
  {
    // scan config dir and loading configs
    $files = scandir(CONFIG_PATH);
    foreach ($files as $file) {
      if ($file === '.' || $file === '..') {
        continue;
      }

      $configs = include CONFIG_PATH . $file;
      $name = str_replace(static::$_extension, '', $file);

      // override configs
      if (isset(static::$_configs[$name])) {
        static::$_configs[$name] = array_merge($configs, static::$_configs[$name]);
      } else {
        static::$_configs[$name] = $configs;
      }
    }

    return static::$_configs;
  }

  public static function set($name, $data): void
  {
    if ($data === null) {
      unset(static::$_configs[$name]);
      return;
    }

    static::$_configs[$name] = $data;
  }

  public static function setValue($name, $key, $value): void
  {
    if (isset(static::$_configs[$name])) {
      static::$_configs[$name][$key] = $value;
    }
  }
}

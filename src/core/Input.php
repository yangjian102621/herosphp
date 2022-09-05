<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\core;

/**
 * Command line action input params
 * ---------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class Input
{
    protected array $_data = [];

    public function __construct(array $data = null)
    {
        if ($data !== null) {
            $this->_data = $data;
        }
    }

    public function get(string $name, mixed $default = null)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        return $default;
    }

    public function getInt(string $name, int $default = 0)
    {
        return intval($this->get($name, $default));
    }

    public function getBool(string $name, bool $default = false)
    {
        return boolval($this->get($name, $default));
    }

    public function getFloat(string $name, float $default)
    {
        return floatval($this->get($name, $default));
    }

    public function has(string $name)
    {
        return isset($this->_data[$name]);
    }

    // get all params
    public function getAll()
    {
        return $this->_data;
    }
}

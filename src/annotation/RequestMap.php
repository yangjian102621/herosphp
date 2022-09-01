<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\annotation;

use Attribute;

/**
 * RequestMap annotation
 *
 * @author RockYang<yangjian102621@gmail.com>
 */
#[Attribute(Attribute::TARGET_METHOD)]
class RequestMap
{
    public function __construct(public string|array $uri, public string|array $method, public string $desc = '')
    {
    }
}

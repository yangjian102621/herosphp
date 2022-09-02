<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

/**
 * 控制器抽象基类, 所有的控制器类都必须继承此类。
 * 每个操作对应一个 public 方法。
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 */

namespace herosphp\core;

use herosphp\utils\StringUtil;
use herosphp\vo\JsonVo;

abstract class BaseController extends Template
{
    protected function init()
    {
    }

    protected function view(string $template, array $data): HttpResponse
    {
        $html = $this->getExecutedHtml($template, $data);
        return new HttpResponse(200, ['Content-Type' => 'text/html', 'X-Powered-By' => X_POWER], $html);
    }

    protected function json(array|JsonVo $data): HttpResponse
    {
        if (is_array($data)) {
            return new HttpResponse(
                200,
                ['Content-Type' => 'application/json', 'X-Powered-By' => X_POWER],
                StringUtil::jsonEncode($data)
            );
        } elseif ($data instanceof JsonVo) {
            return new HttpResponse(200, ['Content-Type' => 'application/json'], $data->toString());
        }

        return new HttpResponse(200, [], '');
    }
}

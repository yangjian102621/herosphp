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

use herosphp\utils\JsonResult;
use herosphp\vo\JsonItem;
use herosphp\vo\JsonVo;
use Workerman\Protocols\Http\Response;

abstract class BaseController extends Template
{

    /**
     * 控制器初始化方法，每次请求必须先调用的方法，action子类可以重写这个方法进行页面的初始化
     */
    protected function C_start()
    {
    }

    protected function view(string $template, array $data): Response
    {
        $html = $this->getExecutedHtml($template, $data);
        return new Response(200, ['Content-Type' => 'text/html'], $html);
    }

    protected function jsonView(int $code, array|JsonVo $data): Response
    {
        if (is_array($data)) {
            return new Response(200, [
                'Content-Type' => 'application/json'
            ], JsonItem::create($code, '', $data)->toString());
        } else if ($data instanceof JsonVo) {
            return new Response(200, ['Content-Type' => 'application/json'], $data->toString());
        }

        return new Response(200, [], '');
    }
}

<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\vo;

class JsonList implements JsonVo
{
    public int $code = 0;

    public string $message;

    public array $items = [];

    public int $total = 0;

    public int $page = 1;

    public int $page_size = 10;

    public mixed $extra_data;

    public function __construct(int $code, string $message, mixed $items)
    {
        $this->code = $code;
        $this->message = $message;
        $this->items = $items;
    }

    public static function create(int $code, string $message, mixed $data)
    {
        return new static($code, $message, $data);
    }

    public function toString(): string
    {
        return json_encode([
            'code' => $this->code,
            'message' => $this->message,
            'items' => $this->items,
            'total' => $this->total,
            'page' => $this->page,
            'page_size' => $this->page_size,
            'extra_data' => $this->extra_data
        ]);
    }
}

<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\vo;

use herosphp\utils\StringUtil;

class JsonItem implements JsonVo
{
    public int $code;

    public string $message;

    public mixed $data;

    public function __construct(int $code, string $message, mixed $data)
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public static function create(int $code, string $message, mixed $data): self
    {
        return new self($code, $message, $data);
    }

    public function toString(): string
    {
        return StringUtil::jsonEncode([
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data
        ]);
    }
}

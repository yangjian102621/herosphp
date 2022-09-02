<?php
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\core;

/**
 * Session error Enum
 */
enum SessionError: int
{

        // error codes
    case OK = 0;

        // client disconnect
    case ERR_LOSE_CONNECT = 1;

        // invalid session token, such as the signature is not corrent
    case ERR_INVALID_SESS_TOKEN = 2;

        // client's ip address changed
    case ERR_ADDR_CHANGED = 3;

        // client's User-Agent changed
    case ERR_DEVICE_CHANGED = 4;

        // if login client number > max_client, the first one will be pushed off
    case ERR_PUSHED_OFFLINE = 5;

        // session expired, data be cleaned
    case ERR_SESS_EXPIRED = 6;

    public function getName(): string
    {
        return match ($this) {
            static::OK => 'Success',
            static::ERR_LOSE_CONNECT => 'client disconnect',
            static::ERR_INVALID_SESS_TOKEN => 'invalid session token',
            static::ERR_ADDR_CHANGED => 'client address changed',
            static::ERR_DEVICE_CHANGED => 'client device changed',
            static::ERR_PUSHED_OFFLINE => 'client have be pushed off',
            static::ERR_SESS_EXPIRED => 'session expired'
        };
    }
}

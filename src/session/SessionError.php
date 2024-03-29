<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\session;

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

    public function getName(): string
    {
        return match ($this) {
            SessionError::OK => 'Success',
            SessionError::ERR_LOSE_CONNECT => 'client disconnect',
            SessionError::ERR_INVALID_SESS_TOKEN => 'invalid session token',
            SessionError::ERR_ADDR_CHANGED => 'client address changed',
            SessionError::ERR_DEVICE_CHANGED => 'client device changed',
            SessionError::ERR_PUSHED_OFFLINE => 'client have be pushed off',
        };
    }
}

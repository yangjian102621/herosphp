<?php
/*---------------------------------------------------------------------
 * api 异常类
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\api;

use herosphp\exception\HeroException;

class APIException extends HeroException {

    public function __construct($code, $message ) {
        parent::__construct($message, $code);
    }

}
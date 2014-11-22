<?php
/*---------------------------------------------------------------------
 * HerosPHP 框架异常处理基类
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\exception;

class HeroException extends \Exception {

    public function __construct( $message, $code ){
        parent::__construct($message, $code);
    }
} 
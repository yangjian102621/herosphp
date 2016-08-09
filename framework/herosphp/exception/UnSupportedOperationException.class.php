<?php
/*---------------------------------------------------------------------
 * 不支持的操作异常
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\exception;

class UnSupportedOperationException extends HeroException {

    public function __contruct() {
        parent::__contruct("抱歉，暂时不支持次操作.");
    }

}
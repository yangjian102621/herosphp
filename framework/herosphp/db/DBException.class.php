<?php
/*---------------------------------------------------------------------
 * 数据库异常处理类
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\db;

use herosphp\exception\HeroException;

class DBException extends HeroException {

    protected $query;       /* 查询语句 */

    public function __contruct( $message ) {
        parent::__contruct($message);
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param $code
     */
    public function setCode($code) {
        $this->code = $code;
    }

}
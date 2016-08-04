<?php

namespace herosphp\lock\interfaces;

/*---------------------------------------------------------------------
 * 同步锁接口
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

interface ISynLock {

    /**
     * 尝试去获取锁，成功返回false并且一直阻塞
     * @return mixed
     */
    public function tryLock();

    /**
     * 释放锁
     * @return mixed
     */
    public function unlock();

} 
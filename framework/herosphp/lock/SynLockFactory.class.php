<?php

namespace herosphp\lock;

/*---------------------------------------------------------------------
 * 同步锁工厂类
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

use herosphp\lock\interfaces\ISynLock;

class SynLockFactory {

    private static $_FILELOCK_POOL = array(); //文件锁池

    private static $_SEMLOCK_POOL = array(); //信号量锁池

    /**
     * 获取文件锁
     * @param $key
     * @return ISynLock
     */
    public static function getFileSynLock($key) {

        if ( !isset(self::$_FILELOCK_POOL[$key])  ) {
            self::$_FILELOCK_POOL[$key] = new FileSynLock($key);
        }
        return self::$_FILELOCK_POOL[$key];
    }

    /**
     * 获取信号量锁
     * @param $key
     * @return ISynLock
     */
    public static function getSemSynLock($key) {

        if ( !isset(self::$_SEMLOCK_POOL[$key])  ) {
            self::$_SEMLOCK_POOL[$key] = new SemSynLock($key);
        }
        return self::$_SEMLOCK_POOL[$key];
    }

} 
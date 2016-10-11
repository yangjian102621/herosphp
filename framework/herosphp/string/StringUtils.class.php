<?php

namespace herosphp\string;

/*---------------------------------------------------------------------
 * 字符串工具类
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

use herosphp\lock\SynLockFactory;

class StringUtils {

    const UUID_LOCK_KEY = 'herosphp_uuid_lock_key';

    /**
     * 生成一个唯一分布式UUID,根据机器不同生成
     * @param bool $forceUnique 是否强制唯一性，这样性能会低一些，但是可以保证绝对唯一
     * @param int $bit UUID的位数,默认24位
     * @return mixed
     */
    public static function genGlobalUid($forceUnique=true, $bit=24) {

        //生成前4位，通过服务器节点避免不同服务器分布式的同步执行造成同步
        $prefix = null;
        if ( defined('SERVER_NODE_NAME') ) {
            $prefix = substr(md5(SERVER_NODE_NAME), 0, 4);
        } else {
            $prefix = sprintf("%04x", mt_rand(0, 0xffff));
        }

        //获取同步锁，睡5微秒，保证绝对唯一性
        if ( $forceUnique ) {
            $lock = SynLockFactory::getFileSynLock(self::UUID_LOCK_KEY);
            $lock->tryLock();
            usleep(5);
        }

        $tArr = explode(' ', microtime());
        $tsec = $tArr[1];
        $msec = $tArr[0];
        if ( ($sIdx = strpos($msec, '.')) !== false ) {
            $msec = substr($msec, $sIdx + 1);
        }

        if ( $forceUnique ) $lock->unlock();

        if ( $bit == 32 ) {
            return sprintf(
                "%0s%08x%08x%04x%04x%04x",
                $prefix,
                $tsec,
                $msec,
                mt_rand(0, 0xffff), //增加随机性，减少重复
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff)
            );
        } else {
            return sprintf(
                "%0s%08x%08x%04x",
                $prefix,
                $tsec,
                $msec,
                mt_rand(0, 0xffff) //增加随机性，减少重复
            );
        }
    }

    /**
     * 将中文数组json编码
     * @param $array
     * @return string
     */
    public static function jsonEncode($array) {
        return urlencode(json_encode($array));
    }

    /**
     * 中文 json 数据解码
     * @param $string
     * @return mixed
     */
    public static function jsonDecode($string) {

        $string = urldecode($string);
        return json_decode($string, true);
    }
} 
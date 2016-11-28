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
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 中文 json 数据解码
     * @param $string
     * @return mixed
     */
    public static function jsonDecode($string) {
        return json_decode($string, true);
    }

    /**
     * 下划线转驼峰
     * @param $str
     * @return string
     */
    public static function underline2hump($str) {

        $str = trim($str);
        if ( strpos($str, "_") === false ) return $str;

        $arr = explode("_", $str);
        $__str = $arr[0];
        for( $i = 1; $i < count($arr); $i++ ) {
            $__str .= ucfirst($arr[$i]);
        }
        return $__str;
    }

    /**
     * 驼峰转下划线
     * @param $str
     * @return mixed
     */
    public static function hump2Underline($str) {
        $arr = array();
        for( $i = 1; $i < strlen($str); $i++ ) {
            if ( ord($str[$i]) > 64 && ord($str[$i]) < 91 ) {
                $arr[] = "_".strtolower($str[$i]);
            } else {
                $arr[] = $str[$i];
            }
        }
        return implode('', $arr);
    }
} 
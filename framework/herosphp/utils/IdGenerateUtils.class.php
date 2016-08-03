<?php

namespace herosphp\utils;

/*---------------------------------------------------------------------
 * 唯一ID生成工具类
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

class IdGenerateUtils {

    /**
     * 生成一个32位唯一分布式UUID,根据机器不同生成
     * @param string $prefix
     * @return mixed
     */
    public static function createUUID( $prefix='' ) {
        static $guid;
        $uid = uniqid($prefix, true);
        $data = $prefix;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        $data .= $_SERVER['LOCAL_ADDR'];
        $data .= $_SERVER['LOCAL_PORT'];
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash,  0,  8)
            . '-' . substr($hash,  8,  4)
            . '-' . substr($hash, 12,  4)
            . '-' . substr($hash, 16,  4)
            . '-' . substr($hash, 20, 12);
        if ( $prefix != '' ) {
            $guid = strtoupper($prefix).'-'.$guid;
        }
        return $guid;
    }

    /**
     * 创建一个18位的唯一的数字ID
     * @param $serverID 服务器ID，分布式服务的服务器的编号
     * @return
     */
    public static function createNID($serverID=1) {

        $factor = 1260000000000;    //初始化因子
        $time = microtime(true) * 10000 - $factor;
        $infoid = ($time << 7) | $serverID;
        usleep(50); //睡50微秒，防止在同一时间并发
        return $infoid;
    }
} 
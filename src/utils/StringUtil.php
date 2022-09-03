<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

/**
 * 字符串工具类
 * ---------------------------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */

namespace herosphp\utils;

use herosphp\exception\HeroException;
use herosphp\GF;

class StringUtil
{
    public const UUID_LOCK_KEY = 'herosphp_uuid_lock_key';

    /**
     * 生成一个唯一分布式UUID,根据机器不同生成. 长度为18位。
     * 机器码(2位) + 时间(12位，精确到微秒)
     * @return mixed
     */
    public static function genGlobalUid(): string
    {
        $lock = Lock::get(self::UUID_LOCK_KEY);
        if ($lock->tryLock()) {
            //获取服务器时间，精确到毫秒
            $tArr = explode(' ', microtime());
            $tsec = $tArr[1];
            $msec = $tArr[0];
            if (($sIdx = strpos($msec, '.')) !== false) {
                $msec = substr($msec, $sIdx + 1);
            }

            //获取服务器节点机器 ID
            $mid = GF::getAppConfig('machine_id');
            if (!$mid) {
                $mid = 0x01;
            }
            $lock->unlock();

            return sprintf(
                '%02x%08x%08x',
                $mid,
                $tsec,
                $msec
            );
        }
        throw new HeroException('failed to aquire the lock.');
    }

    public static function jsonEncode($array)
    {
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    public static function jsonDecode($string)
    {
        $string = trim($string, "\xEF\xBB\xBF");
        return json_decode($string, true);
    }

    // 下划线转驼峰
    public static function ul2hump($str)
    {
        $str = trim($str);
        if (!str_contains($str, '_')) {
            return $str;
        }

        $arr = explode('_', $str);
        $__str = $arr[0];
        for ($i = 1; $i < count($arr); $i++) {
            $__str .= ucfirst($arr[$i]);
        }
        return $__str;
    }

    // 驼峰转下划线
    public static function hump2ul($str)
    {
        $arr = [];
        for ($i = 1; $i < strlen($str); $i++) {
            if (ord($str[$i]) > 64 && ord($str[$i]) < 91) {
                $arr[] = '_' . strtolower($str[$i]);
            } else {
                $arr[] = $str[$i];
            }
        }
        return implode('', $arr);
    }

    // 将16进制的颜色转成成RGB
    public static function hex2rgb($hexColor)
    {
        $color = str_replace('#', '', $hexColor);
        //1.六位数表示形式
        if (strlen($color) > 3) {
            $rgb = [
                'r' => hexdec(substr($color, 0, 2)),
                'g' => hexdec(substr($color, 2, 2)),
                'b' => hexdec(substr($color, 4, 2))
            ];

        //2. 三位数表示形式
        } else {
            $color = $hexColor;
            $r = substr($color, 0, 1) . substr($color, 0, 1);
            $g = substr($color, 1, 1) . substr($color, 1, 1);
            $b = substr($color, 2, 1) . substr($color, 2, 1);
            $rgb = [
                'r' => hexdec($r),
                'g' => hexdec($g),
                'b' => hexdec($b)
            ];
        }
        return $rgb;
    }

    // 生成随机字符串
    public static function genRandomStr($length): string
    {
        $letters = [
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '0',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
            'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
            'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S',
            'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ];
        $str = [];
        $count = count($letters);
        while ($length-- > 0) {
            $str[] = $letters[mt_rand() % $count];
        }
        return implode('', $str);
    }
}

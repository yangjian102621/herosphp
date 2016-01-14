<?php

namespace herosphp\utils;

/*---------------------------------------------------------------------
 * hash 函数工具
 * @package herosphp\utils
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

class HashUtils {

    /**
     * 采用bkdr算法计算hash值
     * @param string $str
     * @return int
     */
    public static function BKDRHash( $str ) {
        if ( is_numeric($str) ) {
            return ($str & 0x7FFFFFFF);
        }
        $hcode = 0;
        $len = strlen($str);
        $seed = 31;    // 31 131 1313 13131 131313 etc..
        for ( $i = 0; $i < $len; $i++ ) {
            $hcode = (int) ($hcode * $seed + ord($str[$i]));
        }

        return ($hcode & 0x7FFFFFFF);
    }

    /**
     * JS hash 算法， invented by Justin Sobel
     * @param string $str
     * @return int
     */
    public static function JSHash( $str ) {
        $hcode = 0;
        $len = strlen($str);
        for ( $i = 0; $i < $len; $i++ ) {
            $hcode ^= ( ($hcode << 5) + (ord($str[$i])) + ($hcode << 2) );
        }
        return ($hcode & 0x7FFFFFFF);
    }

    /**
     * DJP hash 算法.
     * invented by doctor Daniel J. Bernstein.
     * @param $str
     * @return int
     */
    public static function DJPHash( $str ) {

        //$hcode = 5381;
        $hcode = 53;
        $len = strlen($str);
        for ( $i = 0; $i < $len; $i++ ) {
            $hcode += ($hcode << 5) + ord($str[$i]);
        }
        return ($hcode & 0x7FFFFFFF);
    }
} 
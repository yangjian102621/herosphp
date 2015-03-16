<?php

namespace herosphp\utils;

/*---------------------------------------------------------------------
 * HerosPHP 数组操作工具类
 * @package herosphp\utils
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

class ArrayUtils {

    /**
     * 更改hash数组的key值, 注意：如果key不唯一则会产生覆盖
     * @param           array $array
     * @param           string $key
     * @return          array
     */
    public static function &changeArrayKey( &$array, $key='id' ) {
        $newArray = array();
        foreach ( $array as $value ) $newArray[$value[$key]] = $value;
        return $newArray;
    }

    /**
     * 按照某一键值过滤数组，只适用与 key => value数组
     *
     * @param   string $key 要筛选的键
     * @param   mixed $val 筛选的边界值(多个边界值可以用数组)
     * @param   array $array 被筛选的数组
     * @return  array
     */
    function &filterArrayByKey( $key, $val, &$array ) {

        $newArray = array();
        foreach ( $array as $value ) {

            if ( $value[$key]  == $val
                || (is_array($val) && in_array($value[$key], $val)) )
                $newArray[] = $value;

        }
        return $newArray;
    }

    /**
     * 判断一个数组是否是序列化数组
     * @param $data
     * @return bool
     */
    public static function isSerializedArray( $data ) {
        $data = trim( $data );
        return (unserialize($data) != false);
    }
} 
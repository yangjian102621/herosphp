<?php

namespace herosphp\filter;

/*---------------------------------------------------------------------
 * 数据过滤器
 * @package herosphp\filter
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

//数据类型
define('DFILTER_LATIN', 		1 << 0);    //简单字符
define('DFILTER_URL', 		    1 << 1);    //url
define('DFILTER_EMAIL', 		1 << 2);    //email
define('DFILTER_NUMERIC', 	    1 << 3);    //数字
define('DFILTER_STRING',		1 << 4);    //字符串
define('DFILTER_MOBILE', 	    1 << 5);    //手机号码
define('DFILTER_TEL', 		    1 << 6);    //电话号码
define('DFILTER_IDENTIRY', 	    1 << 7);    //身份证
define('DFILTER_REGEXP', 	    1 << 8);    //正则表达式

//数据的净化
define('DFILTER_SANITIZE_TRIM', 	1 << 0);    //去空格
define('DFILTER_SANITIZE_SCRIPT', 	1 << 1);    //去除javascript脚本
define('DFILTER_SANITIZE_HTML', 	1 << 2);    //去除html标签
define('DFILTER_MAGIC_QUOTES', 		1 << 3);    //去除sql注入
define('DFILTER_SANITIZE_INT', 		1 << 4);    //转整数

class Filter {

    public static function init() {

        //do nothing here
    }

    /**
     * 判断是否拉丁字符
     * @param $value
     * @return bool
     */
    private static function isLatin( &$value ) {
        return (preg_match('/^[a-z0-9_]+$/i', $value) == 1);
    }

    /**
     * 判断是否url
     * @param $value
     * @return bool
     */
    private static function isUrl( &$value ) {
        return (filter_var($value, FILTER_VALIDATE_URL) != FALSE);
    }

    /**
     * 判断是否邮箱
     * @param $value
     * @return bool
     */
    private static function isEmail( &$value ) {
        return (filter_var($value, FILTER_VALIDATE_EMAIL) != FALSE);
    }

    /**
     * 是否字符串
     * @param $value
     * @return bool
     */
    private static function isString( &$value ) {
        return is_string($value) && (trim($value) != '');
    }

    /**
     * 判断是否邮编
     * @param $value
     * @return bool
     */
    private static function isZip( &$value ) {
        return (preg_match('/^[0-9]{6}$/', $value) == 1);
    }

    /**
     * 判断是否手机号码
     * @param $value
     * @return bool
     */
    private static function isMobile( &$value ) {
        return (preg_match('/^1[3|5|4|8][0-9]{9}$/', $value) == 1);
    }

    /**
     * 判断是否电话号码
     * @param $value
     * @return bool
     */
    private static function isTelephone( &$value ) {
        return (preg_match('/^0[1-9][0-9]{1,2}-[0-9]{7,8}$/', $value) == 1);
    }

    /**
     * 检验身份证号码是否合格
     * @param $value
     * @return bool
     */
    private static function isIdentity( &$value ) {
        if ( strlen($value) != 15 && strlen($value) != 18 )
            return false;
        //如果是15位的身份证号码则转换位18位的身份证号码
        if ( strlen($value) == 15 ) $value = self::idcard_15to18($value);

        return self::idcard_checksum18($value);
    }

    /**
     * 正则验证
     * @param $value
     * @param $pattern
     * @return int
     */
    private static function pregCheck(&$value, $pattern) {

        if ( !is_string($pattern) ) return false;
        return preg_match($pattern, $value);
    }

    /**
     * 计算身份证号码中的检校码
     * @param string $idcard_base 身份证号码的前十七位
     * @return string 检校码
     */
    private static function idcard_verify_number( $idcard_base ) {

        if ( strlen($idcard_base) != 17 ) return false;

        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ( $i = 0; $i < strlen($idcard_base); $i++ ) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    /**
     * 将15位身份证升级到18位
     * @param string $idcard 十五位身份证号码
     * @return bool|string
     */
    private static function idcard_15to18( $idcard ) {

        if ( strlen($idcard) != 15 ) return false;

        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if ( array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false ) {
            $idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 15);
        } else {
            $idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 15);
        }

        $idcard = $idcard . self::idcard_verify_number($idcard);
        return $idcard;
    }

    /**
     * 18位身份证校验码有效性检查
     * @param string $idcard 十八位身份证号码
     * @return bool
     */
    private static function idcard_checksum18( $idcard ) {

        if ( strlen($idcard) != 18 ) return false;

        $idcard_base = substr($idcard, 0, 17);
        if ( self::idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1)) ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 去除html标签
     * @param $value
     * @return mixed
     */
    private static function sanitizeHtml( &$value ) {
        //sanitize regex rules
        $_rules = array(
            '/<[^>]*?\/?>/is' => ''
        );

        return preg_replace(array_keys($_rules), $_rules, $value);
    }

    /**
     * 去除javascript标签
     * @param $value
     * @return mixed
     */
    private static function sanitizeScript( &$value ) {
        //1. 去除javascript脚本.
        //2. 移除html节点js事件.
        $_rules = array(
            '/<script[^>]*?>.*?<\/script\s*>/i',
            '/<([^>]*?)on[a-zA-Z]+\s*=\s*".*?"([^>]*?)>/i',
            '/<([^>]*?)on[a-zA-Z]+\s*=\s*\'.*?\'([^>]*?)>/i'
        );

        return preg_replace($_rules, array('', '<$1$2>'), $value);
    }

    /**
     * 检验数据
     * @param $value 要检验的值
     * @param $model 检验规则数据模型
     * @param $error 错误信息
     * @return bool|int|mixed|string
     */
    private static function check( &$value, &$model, &$error )
    {
        //1. 数据类型验证
        if ( $value == null ) return '';

        $error = $model[3]."填写不合格！";
        if ( ($model[0] & DFILTER_LATIN) != 0 )
            if ( ! self::isLatin( $value ) )     return FALSE;
        if ( ($model[0] & DFILTER_URL) != 0 )
            if ( ! self::isUrl( $value ) )       return FALSE;
        if ( ($model[0] & DFILTER_EMAIL) != 0 )
            if ( ! self::isEmail( $value ) )     return FALSE;
        if ( ($model[0] & DFILTER_NUMERIC) != 0 )
            if ( ! is_numeric( $value ) )        return FALSE;
        if ( ($model[0] & DFILTER_STRING) != 0 )
            if ( ! self::isString( $value ) )    return FALSE;
        if ( ($model[0] & DFILTER_ZIP) != 0 )
            if ( ! self::isZip( $value ) )       return FALSE;
        if ( ($model[0] & DFILTER_MOBILE) != 0 )
            if ( ! self::isMobile( $value ) ) return FALSE;
        if ( ($model[0] & DFILTER_TEL) != 0 )
            if ( ! self::isTelephone( $value ) )       return FALSE;
        if ( ($model[0] & DFILTER_IDENTIRY) != 0 )
            if ( ! self::isIdentity( $value ) )  return FALSE;
        if ( ($model[0] & DFILTER_REGEXP) != 0 )
            if ( ! self::pregCheck( $value, $model[1] ) )  return FALSE;

        //2. 数据长度验证
        if ( $model[1] != null ) {
            if ( $model[1][0] > 0 ) {
                if ( mb_strlen($value, "UTF-8") < $model[1][0] ) {
                    $error = $model[3]."数据小于指定长度！";
                    return FALSE;
                }
            }
            if ( $model[1][1] > 0 ) {
                if ( strlen($value) > $model[1][1] ) {
                    $error = $model[3]."数据大于指定长度！";
                    return FALSE;
                }
            }
        }

        $error = null;

        //3. 数据净化
        if ( $model[2] == null ) return $value;
        if ( ( $model[2] & DFILTER_SANITIZE_TRIM ) != 0 )
            $value = trim($value);
        if ( ( $model[2] & DFILTER_SANITIZE_SCRIPT ) != 0 )
            $value = self::sanitizeScript($value);
        if ( ( $model[2] & DFILTER_SANITIZE_HTML ) != 0 )
            $value = self::sanitizeHtml($value);
        if ( ( $model[2] & DFILTER_SANITIZE_INT ) != 0 )
            $value = intval( $value );
        if ( ( $model[2] & DFILTER_MAGIC_QUOTES ) != 0
            && !get_magic_quotes_gpc() )
            $value = addslashes( $value );

        return $value;
    }

    /**
     * 过滤指定的数据
     * @param $value
     * @param $model
     * @param $error
     * @return bool|int|mixed|string
     */
    public static function filterVar( $value, $model, &$error ) {
        return self::check($value, $model, $error);
    }

    /**
     * 从数据模型中获取过滤后的数据
     * @param $src  表单的原始数据
     * @param $model 检验规则数据模型
     * @param $error 错误信息
     * @return array|bool
     */
    public static function loadFromModel( &$src, $model, &$error )
    {
        $data = array();
        foreach ( $src as $key => $value ) {
            if ( is_array($model[$key]) ) {
                $result = self::filterVar($value, $model[$key], $error);

                if ( $result === FALSE ) return FALSE;

                //存储过滤后的数据
                $data[$key] = $result;

            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }


} 
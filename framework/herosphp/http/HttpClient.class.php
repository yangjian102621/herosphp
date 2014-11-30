<?php
/*---------------------------------------------------------------------
 * PHP模拟http GET POST 方法
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\http;

class HttpClient {

    public static $time_out = 30;

    /**
     * 发送POST请求
     * @param $_url
     * @param $_array
     * @return array|bool
     */
    public static function post( $_url, $_array ) {
        //构建参数
        $urlArr = parse_url($_url);
        $urlArr['path'] = ($urlArr['path']=='') ? '/':$urlArr['path'];
        $urlArr['port'] = ($urlArr['port']=='') ? '80':$urlArr['port'];

        //打开socket连接
        $handle = fsockopen($urlArr['host'], $urlArr['port'], $errorno, $err_str, self::$time_out);
        if ( $handle == FALSE )
            return FALSE;


        $_arguments = '';
        foreach ( $_array as $_name => $_value ) {
            $_item = $_name.'='.$_value;
            $_arguments .= ($_arguments == '')?$_item:'&'.$_item;
        }

        //创建请求头信息
        $_out  = "POST ".$urlArr['path']." HTTP/1.0\r\n";
        $_out .= "Accept: */*\r\n";
        $_out .= "Host: ".$urlArr['host']."\r\n";
        $_out .= "User-Agent: Lowell-Agent\r\n";
        $_out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $_out .= "Content-Length: ".strlen($_arguments)."\r\n";
        $_out .= "Connection: Close\r\n";
        $_out .= "\r\n";
        $_out .= $_arguments."\r\n\r\n";

        //发送请求
        if ( fwrite($handle, $_out) == FALSE ) {
            fclose($handle);
            return FALSE;
        }

        //获取响应
        $_return = '';
        while ( ! feof($handle) )
            $_return .= fgets( $handle, 2048 );

        $rArr = array();
        //截取头信息和响应正文
        $_pos = stripos($_return, "\r\n\r\n");
        $rArr['head'] = trim(substr($_return, 0, $_pos));
        $rArr['body'] = trim(substr($_return, $_pos));

        fclose($handle);
        return $rArr;
    }

    /**
     * 发送GET请求
     * @param $_url
     * @return array|bool
     */
    public static function get( $_url ) {
        $urlArr = parse_url($_url);
        $urlArr['path'] = ($urlArr['path']=='') ? '/':$urlArr['path'];
        $urlArr['port'] = ($urlArr['port']=='') ? '80':$urlArr['port'];

        $handle = fsockopen($urlArr['host'], $urlArr['port'], $errorno, $err_str, self::$time_out);
        if ( $handle == FALSE )
            return FALSE;

        $_query = $urlArr['path'] . ($urlArr['query']==''?'':'?'.$urlArr['query']);
        $_query = $_query . ($urlArr['fragment']==''?'':'#'.$urlArr['fragment']);

        //创建http头信息
        $_out  = "GET ".$_query." HTTP/1.0\r\n";
        $_out .= "Accept: */*\r\n";
        $_out .= "Host: ".$urlArr['host']."\r\n";
        $_out .= "User-Agent: Payb-Agent\r\n";
        $_out .= "Connection: Close\r\n";
        $_out .= "\r\n";

        //发送请求
        if ( fwrite($handle, $_out) == FALSE ) {
            fclose($handle);
            return FALSE;
        }

        //获取响应
        $_return = '';
        while ( ! feof($handle) )
            $_return .= fgets( $handle, 2048 );


        $rArr = array();
        //截取头信息和响应
        $_pos = stripos($_return, "\r\n\r\n");
        $rArr['head'] = trim(substr($_return, 0, $_pos));
        $rArr['body'] = trim(substr($_return, $_pos));

        fclose($handle);
        return $rArr;
    }

}
?>
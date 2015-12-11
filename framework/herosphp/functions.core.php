<?php
/*---------------------------------------------------------------------
 * 框架公共的常用的全局函数
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

/**
 * 打印函数, 打印变量(数据)
 */
function __print() {
    $_args = func_get_args();  //获取函数的参数

    if( count($_args) < 1 ) {
        \herosphp\core\Debug::appendMessage('必须为myprint()参数');
        trigger_error('必须为myprint()参数');
        return;
    }

    echo '<div style="width:100%;text-align:left"><pre>';
    //循环输出参数
    foreach( $_args as $_a ){
        if( is_array($_a) ){
            print_r($_a);
            echo '<br />';
        } else if( is_string($_a) ){
            echo $_a.'<br />';
        } else {
            var_dump($_a);
            echo '<br />';
        }
    }
    echo '</pre></div>';
}

/**
 * 终端高亮打印绿色
 * @param $message
 */
function tprintOk( $message ) {

   printf("\033[32m\033[1m{$message}\033[0m\n");

}

/**
 * 终端高亮打印红色
 * @param $message
 */
function tprintError( $message ) {

    printf("\033[31m\033[1m{$message}\033[0m\n");

}

/**
 * 终端高亮打印黄色
 * @param $message
 */
function tprintWarning( $message ) {

    printf("\033[31m\033[1m{$message}\033[0m\n");

}

/**
 * 计算字符串的hash值
 * @param string  $str
 * @return int
 */
function getHashCode( $str ) {

    return \herosphp\utils\HashUtils::BKDRHash($str);

}

/**
 * get format filesize string(获取格式化文件大小字符串)
 * @param 	int			$size
 * @return  string 		$size_str
 */
function formatFileSize( $size ) {
    if ( $size/1024 < 1 ) {
        return $size ." B";
    } else if ( $size/1024 > 1 && $size/(1024*1024) < 1 ) {
        return number_format($size/1024, 2, '.', '') .'KB';
    } else if ( $size/(1024*1024) > 1 && $size/(1024*1024*1024)< 1 ) {
        return number_format($size/(1024*1024), 2, '.', '') ." MB";
    } else {
        return number_format($size/(1024*1024*1024), 2, '.', '')." GB";
    }
}

/**
 * 抛出异常
 * @param $message
 * @param int $code
 * @throws herosphp\exception\HeroException
 */
function E( $message, $code=0 ) {
    $exception = new \herosphp\exception\HeroException($message, $code);
    __print($exception); die();
}

/**
 * 获取当前时间
 * @return 		int
 */
function timer() {
    list($msec, $sec) = explode(' ', microtime());
    return ((float)$msec + (float)$sec);
}

/**
 * 将url转换为标准的pathinfo类型的url
 * @param $url
 * @return string
 */
function url( $url ) {

    $_url = \herosphp\http\HttpRequest::url2Target($url);
    if ( $_url != $url ) {
        $_url = rtrim($_url,'/');
        return $_url;
    }

    $webApp = \herosphp\core\WebApplication::getInstance();
    $sysConfig = $webApp->getConfigs();
    $defaultUrl = $sysConfig['default_url'];
    $actionMap = array();
    $args = '';
    $urlInfo = parse_url($url);
    if ( $urlInfo['path'] && $urlInfo['path'] != '/' ) {
        $filename = str_replace(EXT_URI, '', $urlInfo['path']);
        $filename = rtrim($filename, "/");
        $pathInfo = explode('/', $filename);
        array_shift($pathInfo);

        //提取pathinfo参数
        $paramArr = array();
        if ( count($pathInfo) > 3 ) {
            if ( isset($pathInfo[3]) ) {
                $params = explode('-', $pathInfo[3]);
                for ( $i = 0; $i < count($params); $i++ ) {
                    if ( $i % 2 == 0 ) {
                        if ( trim($params[$i]) == ''
                            || trim($params[$i+1]) == ''
                            || strpos($params[$i], '=') != false ) {
                            continue;
                        }
                        $paramArr[] = $params[$i];
                        $paramArr[] = $params[$i+1];
                    }
                }
            }
        }

        if ( $urlInfo['query'] ) {
            $query = preg_replace('/[&|=]/', '-', $urlInfo['query']);
            if ( $query ) $args .= $args == '' ? $query : '-'.$query;
        }

    }

    //如果没有任何参数，则访问默认页面。如http://www.herosphp.my这种格式
    if ( !$actionMap[0] ) $actionMap[0] = $defaultUrl['module'];
    if ( !$actionMap[1] ) $actionMap[1] = $defaultUrl['action'];
    if ( !$actionMap[2] ) $actionMap[2] = $defaultUrl['method'];

    $newUrl = '/'.implode('/', $actionMap);
    if ( !empty($paramArr) ) {
        $newUrl .= '/'.implode('-', $paramArr);
    }
    if ( trim($args) != '' ) $newUrl .= '/'.$args;
    $newUrl .= EXT_URI;
    $newUrl = rtrim($newUrl,'/');
    return $newUrl;
}

/**
 * 从标准的连接获取原始连接
 * @param $url
 * @return string
 */
function getSourceUrl($url) {

    if ( strpos($url, '?') !== false ) {
        return $url;
    }
    $url = trim($url, '/');
    $url = str_replace(EXT_URI, '', $url);
    if ( ($pos = strpos($url, '/')) === false ) {
        return '/'.$url;
    } else {
        $query = substr($url, $pos+1);
        $params = explode('-', $query);
        $url = substr($url, 0, $pos+1).'?';
        $length = count($params);
        $args = array();
        for ( $i = 0; $i < $length-1; $i++ ) {
            if ( $i % 2 == 0 ) {
                $args[$params[$i]] = $params[$i+1];
            }
        }
    }
    return '/'.$url.http_build_query($args);

}

/**
 * 移除url中的某个参数
 * 注意：$url必须是标准的pathinfo类型的url,如果不是，请调用url函数转换
 * @param $url
 * @param $key
 * @return string
 */
function removeUrlArgs( $url, $key ) {

    $url = trim($url, '/');
    $pos = strrpos($url, '/');
    if ( $pos === FALSE ) return $url;

    //移除后缀
    $url = str_replace(EXT_URI, '', $url);
    $prefix = substr($url, 0, $pos+1);
    //移除所有的参数，只留下root url
    if ( $key == 'all' ) {
        return $prefix;
    } else {
        $args_str = substr($url, $pos+1);
        $args = explode('-', $args_str);
        $length = count($args);
        for ( $i = 0; $i < $length; $i++ ) {
            if ( $args[$i] == $key ) {
                unset($args[$i]);
                unset($args[$i+1]);
            }
        }
        return '/'.$prefix.implode('-', $args);
    }
}

/**
 * 往url中添加参数
 * @param $url
 * @param $key
 * @param $value
 * @return string
 */
function addUrlArgs($url, $key, $value) {

    $url = trim($url, '/');
    $pos = strrpos($url, '/');
    //移除后缀
    $url = str_replace(EXT_URI, '', $url);
    if ( $pos === FALSE ) {
        return '/'.$url.'/'.$key.'-'.$value.EXT_URI;
    } else {
        return '/'.$url.'-'.$key.'-'.$value.EXT_URI;
    }

}

/**
 * 获取配置文档的值
 * @param $key
 * @return mixed
 */
function getConfig($key) {

    $webapp = \herosphp\core\WebApplication::getInstance();
    return $webapp->getConfig($key);
}

/**
 * 获取应用的全不配置信息
 * @return array
 */
function getConfigs() {
    $webapp = \herosphp\core\WebApplication::getInstance();
    return $webapp->getConfigs();
}

/**
 * 将中文数组json编码
 * @param $array
 * @return string
 */
function cn_json_encode($array) {
    return urlencode(json_encode($array));
}

/**
 * 中文 json 数据解码
 * @param $string
 * @return mixed
 */
function cn_json_decode($string) {

    $string = urldecode($string);
    return json_decode($string, true);
}

//跳转到404页面
function page404() {
    header("HTTP/1.0 404 Not Found!");
    die();
}

//转跳301页面
function page301( $url ) {
    header( "HTTP/1.1 301 Moved Permanently" );
    header( "Location: {$url}" );
    die();
}


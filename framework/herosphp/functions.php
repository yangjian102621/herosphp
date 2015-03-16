<?php
/*********************************************************************************
 * 公共的常用的全局函数
 *
 * @author			yangjian<yangjian102621@163.com> QQ:906388445
 * ******************************************************************************/

 /**
  * 创建modelDao对象
  * @param      $_model         数据库模型的名称(一般为数据表名或者表名的映射)
  * @param      $_db_config     要使用的数据库的配置信息
  * @return mixed
  */
function MD( $_model, $_db_config = NULL ) {
     if ( $_model != '' ) return new MysqlModelDao($_model, $_db_config);
     return FALSE;
}

/**
 * 打印函数, 打印变量(数据)
 */
function __print() {
    $_args = func_get_args();  //获取函数的参数
    
    if( count($_args) < 1 ) {
        Debug::appendMessage('必须为myprint()参数');
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

    $sysConfig = \herosphp\core\Loader::config();
    $defaultUrl = $sysConfig['default_url'];
    $actionMap = array();
    $args = '';
    $urlInfo = parse_url($url);
    if ( $urlInfo['path'] ) {
        $filename = str_replace(EXT_URI, '', $urlInfo['path']);
        $pathInfo = explode('/', $filename);
        if ( isset($pathInfo[1]) ) {
            $actionMap = explode('_', $pathInfo[1]);
        }

        //提取参数
        if ( isset($pathInfo[2]) ) {
            $args .= $pathInfo[2];
        }

        if ( $urlInfo['query'] ) {
            $query = preg_replace('/[&|=]/', PARAM_SEP, $urlInfo['query']);
            if ( $query ) $args .= $args == '' ? $query : PARAM_SEP.$query;
        }

    }

    //如果没有任何参数，则访问默认页面。如http://www.herosphp.my这种格式
    if ( !$actionMap[0] ) $actionMap[0] = $defaultUrl['module'];
    if ( !$actionMap[1] ) $actionMap[1] = $defaultUrl['action'];
    if ( !$actionMap[2] ) $actionMap[2] = $defaultUrl['method'];

    $newUrl = '/'.implode(ACMAP_SEP, $actionMap);
    if ( trim($args) != '' ) $newUrl .= '/'.$args;
    $newUrl .= EXT_URI;
    return $newUrl;
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
        return '/'.$url.'/'.$key.PARAM_SEP.$value.EXT_URI;
    } else {
        return '/'.$url.PARAM_SEP.$key.PARAM_SEP.$value.EXT_URI;
    }

}


<?php
/*********************************************************************************
 * 公共的常用的全局函数
 *
 * @author			yangjian<yangjian102621@gmail.com> QQ:906388445
 * ******************************************************************************/

 /**
  * 创建modelDao对象
  * 
  * @param      $_model         数据库模型的名称(一般为数据表名或者表名的映射) 
  * @param      $_db_config     要使用的数据库的配置信息
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

?>
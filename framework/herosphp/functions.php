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
 * 计算字符创的hash值
 * @param       string          $_key
 * @return      int 
 */
function bkdrHash( $_key ) {

    $_key = $_key.'';    //将key转换成字符串
    $hcode = 0;
    $len = strlen($_key);
    for ( $i = 0; $i < $len; $i++ ) {
        $hcode = (int) ($hcode * 1331 + ord($_key[$i]));
    }
    return ($hcode & 0x7FFFFFFF);
}

/**
 * 更改hash数组的key值, 注意：如果key不唯一则会产生覆盖
 * @param           array $_arr
 * @param           string $_key
 * @return          array
 */
function changeArrayKey( $_arr, $_key='id' ) {
    $_new_arr = array();
    foreach ( $_arr as $_v ) $_new_arr[$_v[$_key]] = $_v;
    $_arr = null; 
    unset($_arr);
    return $_new_arr;
}

/**
 * 将小写数字转为大写
 * @param       double $num 要转换的数字
 * @param       bool $flag  是否需要单位
 */
function numberToBig( $num, $flag = TRUE )  {
	  
    $d = array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖');  
    $e = array('元','拾','佰','仟','万','拾万','佰万','仟万','亿','拾亿','佰亿','仟亿','万亿');
    $p = array('分','角');  
    $zheng='整'; //追加"整"字   
}

/**
 * 按照某一键值过滤数组，只适用与 key => value数组
 *
 * @param       string $_key 要筛选的键
 * @param       mixed $_val 筛选的边界值(多个边界值可以用数组)
 * @param       array $_arr 被筛选的数组
 * @param       array       返回被筛选后的数组
 * @return array
 */
function &filterArrayByKey( $_key, $_val, &$_arr ) {
    
    $_new_arr = array();
    foreach ( $_arr as $_v ) {
        
        if ( $_v[$_key]  == $_val 
            || (is_array($_val) && in_array($_v[$_key], $_val)) )
            $_new_arr[] = $_v;
            
    }
    return $_new_arr;
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
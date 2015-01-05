<?php
/**
 * 动态缓存配置信息
 * @author yangjian <yangjian102621@163.com>
 */
return array(

    'cache_dir'		=> APP_RUNTIME_PATH.'cache/'.APP_NAME.'/dynamic/', //缓存根目录
    'expire'		=> 60*60*2			//缓存时间，默认为2小时

);
?>

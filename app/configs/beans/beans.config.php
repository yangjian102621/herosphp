<?php

use herosphp\bean\Beans;
/**
 * 公共模块服务 Beans装配配置
 * @author yangjian102621@gmail.com
 * @since 1.0 - Nov 26, 2012
 */
$beans = array(

    /* 应用程序监听器配置 */
    Beans::BEAN_WEBAPP_LISTENER => array (
        '@type' => Beans::BEAN_OBJECT_ARRAY,
        '@attributes' => array (
            array (
                '@type' => Beans::BEAN_OBJECT,
                '@class' => 'common\listener\URLParseListener'
            )
        )
    ),


);
return $beans;
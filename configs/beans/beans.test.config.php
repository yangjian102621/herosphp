<?php

use herosphp\bean\Beans;
/**
 * 文章模块 Beans装配配置
 * @author yangjian102621@163.com
 * @since 1.0 - Nov 26, 2012
 */
$beans = array(
    'test.user.service' => array(
        '@type' => Beans::BEAN_OBJECT,
        '@class' => 'test\service\UserService'
    ),


);
return $beans;
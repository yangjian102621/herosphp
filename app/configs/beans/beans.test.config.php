<?php

use herosphp\bean\Beans;
/**
 * 测试模块 Beans装配配置
 * @author yangjian102621@gmail.com
 * @since 1.0 - Nov 26, 2012
 */
$beans = array(
    //文章服务
    'test.article.service' => array(
        '@type' => Beans::BEAN_OBJECT,
        '@class' => 'test\service\ArticleService',
        '@attributes' => array(
            '@bean/modelDao'=>array(
                '@type'=>Beans::BEAN_OBJECT,
                '@class'=>'test\dao\ArticleDao',
                '@params' => array('article')
            )
        ),
    ),

    //用户服务
    'test.user.service' => array(
        '@type' => Beans::BEAN_OBJECT,
        '@class' => 'test\service\UserService',
    ),

);
return $beans;
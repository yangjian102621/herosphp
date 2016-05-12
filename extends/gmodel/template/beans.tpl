<?php

use herosphp\bean\Beans;
/**
 * {module}模块 Beans装配配置
 * @author {author}<{email}>
 */
$beans = array(
    //{table_name}服务
    'test.article.service' => array(
        '@type' => Beans::BEAN_OBJECT,
        '@class' => 'test\service\ArticleService',
        '@attributes' => array(
            '@bean/modelDao'=>array(
                '@type'=>Beans::BEAN_OBJECT,
                '@class'=>'test\dao\ArticleDao',
                '@params' => array('article', 'user')
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
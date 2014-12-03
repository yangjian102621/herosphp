<?php

use modphp\bean\Beans;

/**
 * 文章模块 Beans装配配置
 * @author blueyb.java@gmail.com
 * @since 1.0 - Nov 26, 2012
 */
$beans = array(
    'article.article.service' => array(       /* 文章内容服务 */
        '@type' => Beans::TYPE_OBJECT,
        '@class' => 'article\service\ArticleService',
        '@attributes' => array(
            '@bean/modelDao' => array(
                '@type' => Beans::TYPE_OBJECT,
                '@class' => 'article\dao\ArticleDao',
                '@params' => array('Article', 'ArticleData', 'articleAttrAssoc', 'ArticleRecAssoc')
            )
        )
    ),

    'article.category.service' => array(       /* 系统文章分类服务 */
        '@type' => Beans::TYPE_OBJECT,
        '@class' => 'article\service\ArticleCategoryService',
        '@attributes' => array(
            '@bean/modelDao' => array(
                '@type' => Beans::TYPE_OBJECT,
                '@class' => 'article\dao\ArticleCategoryDao',
                '@params' => 'ArticleCategory'
            )
        ),
    ),

    'article.userCategory.service' => array(       /* 用户自定义文章分类服务 */
        '@type' => Beans::TYPE_OBJECT,
        '@class' => 'article\service\ArticleUserCategoryService',
        '@attributes' => array(
            '@bean/modelDao' => array(
                '@type' => Beans::TYPE_OBJECT,
                '@class' => 'article\dao\ArticleUserCategoryDao',
                '@params' => 'ArticleUserCategory'
            )
        ),
    ),
	
	'article.attr.service' => array(       /* 文章属性服务 */
        '@type' => Beans::TYPE_OBJECT,
        '@class' => 'article\service\ArticleAttrService',
        '@attributes' => array(
            '@bean/modelDao' => array(
                '@type' => Beans::TYPE_OBJECT,
                '@class' => 'article\dao\ArticleAttrDao',
                '@params' => array('ArticleAttr', 'articleAttrAssoc')
            )
        ),
    ),
	
	'article.source.service' => array(       /* 文章来源服务 */
        '@type' => Beans::TYPE_OBJECT,
        '@class' => 'article\service\ArticleSourceService',
        '@attributes' => array(
            '@bean/modelDao' => array(
                '@type' => Beans::TYPE_OBJECT,
                '@class' => 'article\dao\ArticleSourceDao',
                '@params' => 'ArticleSource'
            )
        ),
    ),
	
	'article.recommend.service' => array(       /* 文章推荐位服务 */
        '@type' => Beans::TYPE_OBJECT,
        '@class' => 'article\service\ArticleRecService',
        '@attributes' => array(
            '@bean/modelDao' => array(
                '@type' => Beans::TYPE_OBJECT,
                '@class' => 'article\dao\ArticleRecDao',
                '@params' => array('ArticleRec', 'ArticleRecAssoc')
            )
        ),
    )
);
return $beans;
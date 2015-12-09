<?php
/*---------------------------------------------------------------------
 * 当前访问application配置信息.
 * 注意：此处的配置将会覆盖同名键值的系统配置
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

$config = array(

    'site_url' => 'http://www.herosphp.my',     //网站地址
    'domain' => 'http://www.herosphp.my',     //网站域名
    'res_url' => 'http://www.herosphp.my',      //静态资源的服务器地址(css, image)
    //默认访问的页面
    'default_url' => array(
        'module' => 'test',
        'action' => 'index',
        'method' => 'index' ),

    'template' => 'default',    //默认模板
    'temp_cache' => 0,      //模板引擎缓存

    //短链接映射
    'url_mapping_rules' => array(
        //目标链接到源链接
        'target_to_source' => array(
            //新闻详情
            '^\/newsdetail-(\d+)\/?$' => '/article_article_detail/?id=${1}',
            //新闻详情分页
            '^\/newsdetail-(\d+)-(\d+)\/?$' => '/article_article_detail/?id=${1}&page=${2}',
            //新闻列表
            '^\/newslist-(\d+)\/?$' => '/article_article_index/?id=${1}',
            //新闻列表页分页
            '^\/newslist-(\d+)-(\d+)\/?$' => '/article_article_index/?id=${1}&page=${2}',
            //关于我们
            '^\/service\/id-(\d+)\.shtml$' => '/article_artone_service/?id=${1}',
            //所有媒体列表
            '^\/medias\/?$' => '/article_media_index',
            //所有媒体列表分页
            '^\/medias-(\d+)\/?$' => '/article_media_index/?page=${1}',
            //媒体分类列表
            '^\/medias\/([a-z|A-Z|0-9|_]+)\/?$' => '/article_media_index/?tkey=${1}',
            //媒体分类列表分页
            '^\/medias\/([a-z|A-Z|0-9|_]+)-(\d+)\/?$' => '/article_media_index/?tkey=${1}&page=${2}',
            //标签详情
            '^\/tags-(\d+)\/?$' => '/article_tags_detail/?id=${1}',

        ),

        //源链接到目标链接
        'source_to_target' => array(
            //新闻详情
            '^\/article_article_detail\/\?id=(\d+)$' => '/newsdetail-${1}/',
            //新闻详情分页
            '^\/article_article_detail\/\?id=(\d+)&page=(\d+)$' => '/newsdetail-${1}-${2}/',
            //新闻列表
            '^\/article_article_index\/\?id=(\d+)$' => '/newslist-${1}/',
            //新闻列表页分页
            '^\/article_article_index\/\?id=(\d+)&page=(\d+)$' => '/newslist-${1}-${2}/',
            //关于我们
            '^\/article_artone_service\/\?id=(\d+)$' => '/service/id-${1}.shtml',
            //有所媒体列表
            '^\/article_media_index\/?$' => '/medias/',
            //有所媒体列表分页
            '^\/article_media_index\/\?page=(\d+)$' => '/medias-${1}/',
            //媒体分类列表
            '^\/article_media_index\/\?tkey=([a-z|A-Z|0-9|_]+)$' => '/medias/${1}/',
            //媒体分类列表分页
            '^\/article_media_index\/\?tkey=([a-z|A-Z|0-9|_]+)&page=(\d+)$' => '/medias/${1}-${2}/',
            //标签详情
            '^\/article_tags_detail\/\?id=(\d+)$' => '/tags-${1}/',
        ),
    ),

);

return $config;
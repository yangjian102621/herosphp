<?php
/*---------------------------------------------------------------------
 * 文件缓存抽象类
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\cache;

Abstract class ACache {

    /**
     * 每个缓存文件夹的文件容量
     * @var int
     */
    protected static $_FILE_OPACITY = 1000;

    /**
     * 缓存配置参数
     * @var array
     */
    protected $configs = array();

    /**
     * 缓存的基础路径,最好有语义,推荐使用action名称,  如article
     * @var string
     */
    protected $baseKey = 'default';

    /**
     * 缓存分类目录， 推荐使用当前调用的method操作,如 index,list,detail等
     * @var string
     */
    protected $ftype = null;

    /**
     * 缓存的分类因子,一般来说
     * 1. 如果是列表页,推荐使用页码$page
     * 2. 如果是详情页，推荐使用$id
     * @var int
     */
    protected $factor = null;

    /**
     * 缓存key,对于没有规则的杂乱缓存，必须为其设置一个key作为hash散列作为文件分类
     * @var string
     */
    protected $key = null;

    /**
     * 初始化缓存配置信息
     * @param array $configs 缓存配置信息
     */
    public function __construct( $configs ) {
        if ( !$configs ) {
            E("必须传入缓存配置信息！");
        }
        $this->configs = $configs;
    }

    /**
     * 设置当前baseKey
     * @param $baseKey
     */
    public function baseKey( $baseKey = null ) {
        if ( $baseKey )
            $this->baseKey = $baseKey;
    }

    /**
     *
     * @param $factor
     */
    public function factor( $factor ) {

    }

    /**
     * 获取缓存文件路径
     * @return      string
     */
    public function getCacheFile()
    {
        $cacheDir = $this->configs['cache_dir'];

    }


}

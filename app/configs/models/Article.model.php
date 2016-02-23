<?php
/**
 * 文章表数据模型操作类，继承模型基类
 * @author  yangjian <yangjian102621@gmail.com>
 */

namespace app\models;

use herosphp\model\C_Model;

class ArticleModel extends C_Model {

    public function __construct() {

        //创建model对象并初始化数据表名称
        parent::__construct('article');

        //设置表数据表主键，默认为id
        $this->setPrimaryKey('id');

        //设置字段的别名
        $this->setMapping(array(
            'bcontent' => 'sdesc',
        ));

        //初始化数据模型过滤器
        $filterMap = array(
            'title' => array(null, array(10, 30), DFILTER_SANITIZE_TRIM|DFILTER_SANITIZE_HTML, '文章标题'),
            'content' => array(null, null, DFILTER_MAGIC_QUOTES, '文章内容'),
        );
        $this->setFilterMap($filterMap);
    }
} 
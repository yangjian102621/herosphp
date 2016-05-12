<?php
/**
 * admin 数据表模型
 * @author  yangjian <yangjian102621@gmail.com>
 */

namespace app\models;

use herosphp\model\C_Model;

class ArticleModel extends C_Model {

    public function __construct() {

        //创建model对象并初始化数据表名称
        parent::__construct('admin');

        //设置表数据表主键，默认为id
        $this->setPrimaryKey('id');
    }
} 
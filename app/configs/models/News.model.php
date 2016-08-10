<?php
/**
 * user 数据表模型
 * @author  yangjian <yangjian102621@gmail.com>
 */

namespace app\models;

use herosphp\model\MongoModel;

class NewsModel extends MongoModel {

    public function __construct() {

        //创建model对象并初始化数据表名称
        parent::__construct('news');
    }
} 
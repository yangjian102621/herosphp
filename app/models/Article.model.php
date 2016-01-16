<?php
/**
 * 文章表数据模型操作类，继承模型基类
 *
 * @author  yangjian <yangjian102621@gmail.com>
 */

namespace app\models;

use herosphp\model\C_Model;

class ArticleModel extends C_Model {

    public function __construct() {

        parent::__construct('article');
        $this->setPrimaryKey('id');
        $this->setMapping(array(
            'bcontent' => 'sdesc',
        ));
    }
} 
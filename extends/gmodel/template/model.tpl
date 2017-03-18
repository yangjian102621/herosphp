<?php
/**
 * {desc}
 * @author  {author} <{email}>
 */

namespace models;

use herosphp\model\C_Model;

class {model_name} extends C_Model {

    public function __construct() {

        //创建model对象并初始化数据表名称
        parent::__construct('{table_name}');

        //设置表数据表主键，默认为id
        $this->setPrimaryKey('{pk}');
        {autoPrimaryKey}
        {flagments}
        {sharding_num}
    }
} 
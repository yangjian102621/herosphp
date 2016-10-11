<?php
/**
 * user 数据表模型
 * @author  yangjian <yangjian102621@gmail.com>
 */

namespace models;

use herosphp\filter\Filter;
use herosphp\model\C_Model;

class UserShopModel extends C_Model {

    public function __construct() {

        //创建model对象并初始化数据表名称
        parent::__construct('user_shop');

        //设置表数据表主键，默认为id
        $this->setPrimaryKey('userid');
        
    }
} 
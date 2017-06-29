<?php

/**
 * User Dao
 * @author yangjian<yangjian102621@gmail.com>
 * @date 2017-03-20
 */
namespace app\demo\dao;

use herosphp\model\MysqlModel;

class UserDao extends MysqlModel {

    public function __construct() {

        //创建model对象并初始化数据表名称
        parent::__construct('member');

        //设置表数据表主键，默认为id
        $this->primaryKey = 'id';

    }

}
<?php
/**
 * user 数据表模型
 * @author  yangjian <yangjian102621@gmail.com>
 */

namespace models;

use herosphp\model\SimpleShardingModel;

class UserInfoModel extends SimpleShardingModel {

    public function __construct() {

        //创建model对象并初始化数据表名称
        parent::__construct('user_info');

        //设置表数据表主键，默认为id
        $this->setPrimaryKey('id');

        //分片数量
        $this->shardingNum = 7;

    }
} 
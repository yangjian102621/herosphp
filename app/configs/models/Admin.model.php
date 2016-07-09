<?php
/**
 * admin 数据表模型
 * @author  yangjian <yangjian102621@gmail.com>
 */

namespace app\models;

use herosphp\filter\Filter;
use herosphp\model\C_Model;

class AdminModel extends C_Model {

    public function __construct() {

        //创建model对象并初始化数据表名称
        parent::__construct('admin');

        //设置表数据表主键，默认为id
        $this->setPrimaryKey('id');

        $filterMap = array(
            'title' => array(Filter::DFILTER_STRING, array(6, 12), Filter::DFILTER_SANITIZE_TRIM, '标题'),
            'email' => array(Filter::DFILTER_EMAIL, NULL, NULL, '邮箱'),
            'id_number' => array(Filter::DFILTER_IDENTIRY, NULL, NULL, '身份证号码'),
            'content' => array(Filter::DFILTER_STRING, NULL, Filter::DFILTER_MAGIC_QUOTES, '内容')
        );

        $this->setFilterMap($filterMap);
    }
} 
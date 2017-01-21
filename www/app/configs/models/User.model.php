<?php
/**
 * user 数据表模型
 * @author  yangjian <yangjian102621@gmail.com>
 */

namespace models;

use herosphp\filter\Filter;
use herosphp\model\C_Model;

class UserModel extends C_Model {

    public function __construct() {

        //创建model对象并初始化数据表名称
        parent::__construct('area');

        $this->isFlagment = true;

        //设置表数据表主键，默认为id
        $this->setPrimaryKey('id');

//        $this->filterMap = array(
//            'name' => array(Filter::DFILTER_STRING, array(6, 12), Filter::DFILTER_SANITIZE_TRIM,
//                array("require" => "名字不能为空.", "length" => "名字长度必需在6-12之间.")),
//            'email' => array(Filter::DFILTER_EMAIL, NULL, NULL,
//                array("type" => "请输入正确的邮箱地址.")),
//            'mobile' => array(Filter::DFILTER_MOBILE, NULL, NULL,
//                array("type" => "请输入正确的手机号码.")),
//            'id_number' => array(Filter::DFILTER_IDENTIRY, NULL, NULL,
//                array('type' => '请输入正确的身份证号码.')),
//            'content' => array(Filter::DFILTER_STRING, NULL, Filter::DFILTER_MAGIC_QUOTES|Filter::DFILTER_SANITIZE_HTML,
//                array("require" => "个人简介不能为空."))
//        );

//        $this->flagments = array(
//
//            array(
//                'fields' => 'userid, bcontent, mobile, email',
//                'model' => 'userInfo'
//            ),
//
//            array(
//                'fields' => 'userid, shop_name, shop_address, shop_type',
//                'model' => 'userShop'
//            )
//
//        );

        //设置数据表字段别名映射
//        $maping = array(
//            'add_time' => 'addTime',
//            'last_login_time' => 'lltime'
//        );
//        $this->setMapping($maping);
    }
} 
<?php
/**
 * @author yangjian
 * 自动加载类配置，需要带上命名空间
 */
return array(

    'admin\action\CommonAction'                     => 'admin.action.CommonAction',  //后台控制器基类
    'common\action\CommonAction'                    => 'common.action.CommonAction', //前端控制器基类
    'common\service\interfaces\ICommonService'      => 'common.service.interfaces.ICommonService',  //公共服务接口
    'common\service\CommonService'                  => 'common.service.CommonService', //公共服务实现
    'common\dao\interfaces\ICommonDao'              => 'common.dao.interfaces.ICommonDao', //公共dao接口
    'common\dao\CommonDao'                          => 'common.dao.CommonDao', //公共dao实现

);
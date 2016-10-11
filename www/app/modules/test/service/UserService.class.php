<?php
namespace test\service;

use common\service\CommonService;
use herosphp\core\Loader;
use test\service\interfaces\IUserService;

Loader::import('test.service.interfaces.IUserService', IMPORT_APP);

/**
 * 用户服务实现
 * Class UserService
 * @package test\service
 */
class UserService extends CommonService implements IUserService {
    public function register()
    {
        __print("调用了 UserService::register 方法");
    }

    public function login()
    {
        __print("调用了 UserService::login 方法");
    }
}
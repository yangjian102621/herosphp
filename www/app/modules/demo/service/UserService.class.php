<?php
namespace demo\service;

use common\service\CommonService;
use herosphp\core\Loader;
use demo\service\interfaces\IUserService;

Loader::import('demo.service.interfaces.IUserService', IMPORT_APP);

/**
 * 用户服务实现
 * Class UserService
 * @package demo\service
 */
class UserService extends CommonService implements IUserService {

    public function register()
    {
        printf("调用了 UserService::register 方法");
    }

    public function login()
    {
        printf("调用了 UserService::login 方法");
    }
}
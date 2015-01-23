<?php

namespace test\service;

use herosphp\core\Loader;
use test\service\interfaces\IUserService;
Loader::import('test.service.interfaces.IUserService');

/**
 * Class UserService
 * @package test\service
 */
class UserService implements IUserService {
    /**
     * 登录服务
     * @param $username
     * @param $password
     * @return mixed
     */
    public function login($username, $password)
    {
        __print("Invoked the UserService::login().");
    }

    /**
     * 用户注册
     * @param $userData
     * @return mixed
     */
    public function register($userData)
    {
        __print("Invoked the UserService::register().");
    }

} 
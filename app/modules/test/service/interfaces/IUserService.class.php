<?php

namespace test\service\interfaces;

/**
 * 用户服务接口
 * Interface IUserService
 */
interface IUserService {

    //注册服务
    public function register();

    //登录服务
    public function login();
}

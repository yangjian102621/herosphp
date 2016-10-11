<?php

namespace test\service\interfaces;
use common\service\interfaces\ICommonService;

/**
 * 用户服务接口
 * Interface IUserService
 */
interface IUserService extends ICommonService{

    //注册服务
    public function register();

    //登录服务
    public function login();
}

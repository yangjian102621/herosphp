<?php

namespace test\service\interfaces;

/**
 * Interface IUserService
 */
interface IUserService {

    /**
     * 登录服务
     * @param $username
     * @param $password
     * @return mixed
     */
    public function login($username, $password);

    /**
     * 用户注册
     * @param $userData
     * @return mixed
     */
    public function register( $userData );
}
?>
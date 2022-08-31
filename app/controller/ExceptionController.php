<?php

namespace app\controller;

use app\exception\AuthenticationException;
use app\exception\BizException;
use herosphp\annotation\Controller;
use herosphp\annotation\Get;

#[Controller(name: '异常处理')]
class ExceptionController
{
    #[Get(uri: '/e/biz', desc: '异常测试')]
    public function biz()
    {
        throw new BizException('业务不允许这样做!');
        return 'biz';
    }


    #[Get(uri: '/e/auth', desc: '异常测试')]
    public function auth()
    {
        throw new AuthenticationException('请授权登录!');
        return 'auth';
    }

}

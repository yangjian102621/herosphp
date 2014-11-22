<?php
namespace member\action;

use herosphp\core\Controller;
use herosphp\http\HttpRequest;

/**
 * 用户action
 * @since           2013-12-28
 * @author          yangjian<yangjian102621@gmail.com>
 */
class LoginAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {

        __print($request);
        __print("invoking the index method....");

    }

    /**
     * 登录操作
     * @param HttpRequest $request
     */
    public function signin( HttpRequest $request ) {

        __print($request->getParameters());
        __print("sign in operation....");
    }

  
}
?>

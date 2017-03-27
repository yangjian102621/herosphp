<?php
namespace demo\action;

use herosphp\core\Controller;
use herosphp\http\HttpRequest;
use herosphp\session\Session;
use herosphp\utils\JsonResult;

/**
 * session测试
 * @since           2015-02-21
 * @author          yangjian<yangjian102621@gmail.com>
 */
class SessionAction extends Controller {

    /**
     * 设置session
     * @param HttpRequest $request
     */
    public function set( HttpRequest $request ) {

        //开启session
        Session::start();
        $_SESSION['username'] = 'xiaoyang';
        $_SESSION['password'] = '123456';

        die('设置session成功！');

    }

    /**
     * 获取session
     * @param HttpRequest $request
     */
    public function get( HttpRequest $request ) {

        Session::start();
        __print($_SESSION);
        die();
    }

    public function gc(HttpRequest $request) {
        Session::gc();
        JsonResult::jsonResult(400);
    }
  
}

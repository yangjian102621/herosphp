<?php
namespace test\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\http\HttpRequest;

/**
 * Bean工具测试
 * @since           2015-01-28
 * @author          yangjian<yangjian102621@163.com>
 */
class BeanAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {

        __print($request->getParameters());
        __print("Bean 模块测试，使用bean来管理服务。");
        $userService = Beans::get('test.user.service');
        $userService->login();
        $userService->register();
        die();

    }
  
}
?>

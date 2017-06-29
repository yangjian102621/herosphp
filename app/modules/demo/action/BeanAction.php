<?php
namespace app\demo\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\http\HttpRequest;

/**
 * Bean工具测试
 * @since           2015-01-28
 * @author          yangjian<yangjian102621@gmail.com>
 */
class BeanAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {

        echo("<h1>Bean 模块测试，使用bean来管理服务。Beans::get('test.user.service')</h1>");
        $userService = Beans::get('test.user.service');
        $userService->register();
        $userService->login();
        die();

    }
  
}
?>

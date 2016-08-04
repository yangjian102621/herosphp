<?php
namespace test\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\core\Debug;
use herosphp\core\WebApplication;
use herosphp\http\HttpRequest;
use herosphp\utils\AjaxResult;
use herosphp\web\WebUtils;
use Workerman\Worker;

/**
 * 首页测试
 * @since           2015-01-28
 * @author          yangjian<yangjian102621@gmail.com>
 */
class IndexAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {
        __print("<h1>O(∩_∩)O~~ 欢迎使用Herosphp!</h1>");
        //$this->setView("common:index");
        die();
    }

    //获取用户列表
    public function user() {

        $service = Beans::get('test.user.service');

        $result = $service->fields('*')->where("id > 20")->select();
        __print($result);
        die();

    }
  
}
?>

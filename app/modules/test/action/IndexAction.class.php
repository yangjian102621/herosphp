<?php
namespace test\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\core\Debug;
use herosphp\http\HttpRequest;
use herosphp\utils\AjaxResult;

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
        __print($request->getParameters());
        __print("<h1>Hello， Herosphp!</h1>");

        die();

    }
  
}
?>

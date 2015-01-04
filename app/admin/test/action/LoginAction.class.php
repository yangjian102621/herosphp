<?php
namespace test\action;

use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;

/**
 * 模板解析测试
 * @since           2013-12-28
 * @author          yangjian<yangjian102621@163.com>
 */
class LoginAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {

        __print("invoking the index method....");
        $this->assign('include', '{include admin:member.top}');
        $model = Loader::model('article');
        $items = $model->getItems(null, "id, url, title", null, 1, 20);

        $this->assign('items', $items);

    }

    /**
     * 登录操作
     * @param HttpRequest $request
     */
    public function signin( HttpRequest $request ) {
        __print("sign in operation....");
    }

  
}
?>

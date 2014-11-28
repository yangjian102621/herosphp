<?php
namespace member\action;

use herosphp\core\Controller;
use herosphp\core\Loader;
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

        __print("invoking the index method....");
        $this->assign('include', '{include admin:member.top}');
        $model = Loader::model('article');
        $items = $model->getItems(null, "Id, mtitle");
        __print($items);

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

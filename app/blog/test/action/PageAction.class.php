<?php
namespace test\action;

use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use herosphp\session\FileSession;
use herosphp\utils\FileUtils;
use herosphp\utils\Page;

/**
 * 分页测试
 * @author          yangjian<yangjian102621@163.com>
 */
class PageAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {

        $page = $request->getParameter('pageNow', 'intval');
        $pagesize = 10;

        if ( $page <= 0 ) $page = 1;

        $model = Loader::model('article');
        $conditions = array("id" => ">300");
        $total = $model->count($conditions);
        $items = $model->getItems($conditions, "id, url, title", null, $page, $pagesize);

        //初始化分页类
        $pageHandler = new Page($total, $pagesize, $page);

        $this->assign('pageHandler', $pageHandler);
        $this->assign('items', $items);

    }
  
}
?>

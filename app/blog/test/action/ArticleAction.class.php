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
class ArticleAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {

        $this->assign('include', '{include admin:member.top}');
        $model = Loader::model('article');
        $conditions = array("id" => ">300");
        $items = $model->getItems($conditions, "id, url, title", null, 1, 20);

        $this->assign('items', $items);

    }

    /**
     * @param HttpRequest $request
     * 文章详情
     */
    public function detail( HttpRequest $request ) {
        $id = $request->getParameter('id', 'intval');
        if ( $id <= 0 ) $id = 299;
        $model = Loader::model('article');
        $item = $model->getItem($id);
        __print($item);
        exit();
    }

    /**
     * 更新
     * @param HttpRequest $request
     */
    public function update( HttpRequest $request ) {

        $model = Loader::model('article');
        $id = 4779;
        //$model->increase('hits', 10, $id); //点击率+10
        //$model->reduce('hits', 5, $id);    //点击率-5
        $model->set('hits', 100, $id);   //设置点击率为100
        __print("更新文章 4779 点击量");die();
    }

    /**
     * @param HttpRequest $request
     */
    public function delete( HttpRequest $request ) {

        $id = $request->getParameter('id', 'intval');
        $model = Loader::model('article');
        if ( $id > 0 ) {
            var_dump($model->delete($id));
        }
        die();
    }
  
}
?>

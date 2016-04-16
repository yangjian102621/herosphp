<?php
namespace test\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\exception\HeroException;
use herosphp\http\HttpRequest;

/**
 * 模板解析测试
 * @since           2015-01-12
 * @author          yangjian<yangjian102621@gmail.com>
 */
class ArticleAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {

        $args = $request->getParameter('page', 'intval');
        $model = Loader::model('article');
        $conditions = array("id" => ">100");

        try {
            $items = $model->getItems($conditions, null, null, 1, 30);
        } catch(HeroException $e) {
            //__print($e);
        }

        $this->assign('include', "{include:test.top}");
        $this->assign('args', $args);
        $this->assign('items', $items);

    }

    /**
     * 文章详情
     * @param HttpRequest $request
     */
    public function detail( HttpRequest $request ) {
        $id = $request->getParameter('id', 'intval');
        if ( $id <= 0 ) $id = 4779;

        $articleService = Beans::get('test.article.service');
        $item = $articleService->getItem($id);

        $this->assign('item', $item);
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

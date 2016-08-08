<?php
namespace test\action;

use common\action\CommonAction;
use herosphp\bean\Beans;
use herosphp\db\query\MQuery;
use herosphp\files\FileUtils;
use herosphp\http\HttpRequest;
use herosphp\utils\AjaxResult;
use herosphp\utils\FileUpload;

/**
 * demo action
 * @package commom\action
 * @author yangjian<yangjian102621@gmail.com>
 */
class DemoAction extends CommonAction {

    public function index(HttpRequest $request) {
        $this->setView("index");
        $this->assign("title", "欢迎使用Herosphp");
    }

    //测试联合查询
    public function join() {

        $service = Beans::get('test.user.service');
        //$list = $service->getDB()->getList("select a.id, a.username, a.password, b.title, b.bcontent from fiidee_user a, fiidee_news b where a.id=b.id");

//        $query = MQuery::getInstance()->table("fiidee_user a, fiidee_news b")->field('a.id, a.username, a.password, b.title, b.bcontent')->where('a.id=b.id');
//        $list = $service->getItems($query);

        $query = MQuery::getInstance()->join(" LEFT JOIN fiidee_news a ON {t}.id = a.id")->field("a.title, a.bcontent, {t}.username,{t}.password");
        $list = $service->getItems($query);
        __print($list);

        die();
    }
}

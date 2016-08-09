<?php
namespace test\action;

use common\action\CommonAction;
use herosphp\bean\Beans;
use herosphp\core\Loader;
use herosphp\db\DBFactory;
use herosphp\db\query\MysqlQuery;
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
        //$list = $service->getDB()->getList("select a.id, a.username, a.password, b.title, b.bcontent from fiidee_user a, fiidee_news b where a.id=b.userid");

//        $query = MysqlQuery::getInstance()->table("{prefix}user a, {prefix}article b")->field('a.id, a.username, a.password, b.title, b.bcontent')->where('a.id=b.userid');
//        $list = $service->getItems($query);

        $query = MysqlQuery::getInstance()
            ->leftJoin("{prefix}article a")
            ->on("{t}.id = a.userid")
            ->field("{t}.id, a.title, a.bcontent, {t}.username,{t}.password")
            ->where("{t}.id > 80");
        $list = $service->getItems($query);
        __print($list);

        die();
    }

    public function mongo() {
        $congfig = Loader::config('db');
        $db = DBFactory::createDB('mongo', $congfig['mongo']);
        __print($db);
    }
}

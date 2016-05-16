<?php
namespace common\action;

use herosphp\bean\Beans;
use herosphp\http\HttpRequest;
use Workerman\Protocols\Http;

/**
 * demo action
 * @package commom\action
 * @author yangjian<yangjian102621@gmail.com>
 */
class DemoAction extends CommonAction {

    protected $serviceBean = "user.news.service";

    public function index(HttpRequest $request) {
        $this->setView("index");
        $this->assign("title", "后台管理中心-首页");
    }

    //空白页
    public function blank() {

        $this->setView("blank");
        $this->assign("title", "空白页");

    }

    //列表页
    public function clist(HttpRequest $request) {
        parent::index($request);
        $this->assign("title", "文章列表");
        $this->assign("bread", array("用户管理", "文章管理", "文章列表"));
        $this->setView("clist");
    }
}

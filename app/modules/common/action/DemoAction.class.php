<?php
namespace common\action;

use herosphp\http\HttpRequest;
use herosphp\utils\AjaxResult;

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

    //内容列表页
    public function clist(HttpRequest $request) {

        $title = $request->getParameter("title", "trim");
        $conditions = array();
        if ( $title != "" ) {
            $conditions["title"] = "%{$title}%";
        }
        $this->setConditions($conditions);
        parent::index($request);
        $this->assign("title", "文章列表");
        $this->assign("bread", array("用户管理", "文章管理", "文章列表"));
        $this->setView("clist");
    }

    //内容添加页
    public function cadd() {

        $this->assign("title", "文章添加");
        $this->assign("bread", array("用户管理", "文章管理", "文章添加"));
        $this->setView("cadd");

    }

    //插入操作
    public function insert(HttpRequest $request) {

        AjaxResult::ajaxSuccessResult();
        $data = $request->getParameter("data");
        parent::insert($data);

    }

    //更新操作
    public function update(HttpRequest $request) {

        $data = $request->getParameter("data");
        parent::update($data, $request);
    }
}

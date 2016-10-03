<?php
namespace admin\action;

use herosphp\http\HttpRequest;
use herosphp\utils\AjaxResult;

/**
 * 后台管理 index 控制器
 */
class IndexAction extends CommonAction {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request) {

        $url = $request->getParameter('url', 'trim');
        if ( !$url ) $url = "/admin/index/form";
        $this->setView("index");
        $this->assign("indexPage", $url);

    }

    //添加数据
    public function insert($data) {
        AjaxResult::ajaxSuccessResult();
    }

    public function clist(HttpRequest $request) {

        $this->setView("clist");

    }

    public function remove(HttpRequest $request) {
        AjaxResult::ajaxSuccessResult();
    }

    public function edit(HttpRequest $request) {
        $this->setView("user/edit");
    }

    public function error(HttpRequest $request) {
        $this->showMessage(self::MESSAGE_ERROR, COM_ERR_MSG);
    }
  
}

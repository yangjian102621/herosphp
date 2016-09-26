<?php
namespace admin\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\exception\HeroException;
use herosphp\http\HttpRequest;
use herosphp\utils\AjaxResult;

/**
 * 后台管理 index 控制器
 */
class IndexAction extends Controller {

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
    public function insert(HttpRequest $request) {
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
  
}

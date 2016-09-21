<?php
namespace admin\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\exception\HeroException;
use herosphp\http\HttpRequest;

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

    public function form(HttpRequest $request) {

        $this->setView("form");

    }

    public function clist(HttpRequest $request) {

        $this->setView("list");

    }
  
}

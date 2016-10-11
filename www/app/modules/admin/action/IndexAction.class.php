<?php
namespace admin\action;

use herosphp\http\HttpRequest;
use herosphp\image\VerifyCode;
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

    public function login(HttpRequest $request) {
        $this->setView('login');
    }

    //生成验证码
    public function scode(HttpRequest $request) {
        //画布设置
        $config = array('x'=>10, 'y'=>25, 'w'=>90, 'h'=>36, 'f'=>20);
        $verify = VerifyCode::getInstance();
        $verify->configure($config)->generate(4); //产生5位字符串
        $verify->show('png');
        die();
    }
}

<?php
namespace test\action;

use herosphp\core\Controller;
use herosphp\http\HttpRequest;
use herosphp\session\Session;
use herosphp\utils\AjaxResult;

/**
 * 表单验证测试
 * @since           2015-02-21
 * @author          yangjian<yangjian102621@gmail.com>
 */
class FormAction extends Controller {

    /**
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {

        $data = $request->getParameter('data');
        $this->assign('data', $data);
        $this->setView('form_test');

        $html = $this->getExecutedHtml('form_test');

    }

    /**
     * 检验邮箱
     * @param HttpRequest $request
     */
    public function email( HttpRequest $request ) {

        $email  = $request->getParameter('data');
        AjaxResult::ajaxResult('ok', "邮箱{$email}已经存在!");

    }

}
?>

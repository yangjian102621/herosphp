<?php
namespace user\action;

use common\action\CommonAction;
use herosphp\bean\Beans;
use herosphp\core\WebApplication;
use herosphp\http\HttpRequest;
use PHPExcel\Exception;

/**
 * user action
 * @package user\action
 * @author yangjian<yangjian102621@gmail.com>
 */
class UserAction extends CommonAction {

    public function index(HttpRequest $request) {

        $service = Beans::get('user.user.service');
        $data = array(
            'title' => 'xiaoyang333',
            'email' => '906388445@qq.com',
            'mobile' => '185456701250',
            'id_number' => '431028198801210838',
            'content' => "<span>我有一头'小毛驴'。</span>"
        );
        $result = $service->add($data);
        if ( $result == false ) {
            if ( WebApplication::getInstance()->getAppError()->getCode() != 0 ) {
                throw new Exception(WebApplication::getInstance()->getAppError()->getMessage());
            }
        }
        die();
    }
}

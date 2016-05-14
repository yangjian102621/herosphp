<?php
namespace user\action;

use common\action\CommonAction;
use herosphp\bean\Beans;

/**
 * admin action
 * @package user\action
 * @author yangjian<yangjian102621@gmail.com>
 */
class AdminAction extends CommonAction {

    public function index(HttpRequest $request) {

        echo "<h1>Hello, World.</h1>";

        $adminService = Beans::get("user.admin.service");
        $data = array(
            "user" => "xiaoming",
            "pass" => "xiaoming_pass",
            "role_id" => 1,
            "addtime" => date("Y-m-d H:i:s"),
        );
        var_dump($adminService->add($data));
        //__print($adminService);
        die();

    }
}

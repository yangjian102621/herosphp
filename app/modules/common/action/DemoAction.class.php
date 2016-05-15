<?php
namespace common\action;

use herosphp\http\HttpRequest;

/**
 * demo action
 * @package commom\action
 * @author yangjian<yangjian102621@gmail.com>
 */
class DemoAction extends CommonAction {

    public function index(HttpRequest $request) {
        $this->setView("index");
    }
}

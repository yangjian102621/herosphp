<?php
namespace api\service;
use herosphp\string\StringUtils;
use herosphp\utils\JsonResult;

/**
 * user
 * @author yangjian
 * @email yangjian102621@gmail.com
 * @date 2017-03-26
 */
class UserService {

    /**
     *
     * @param $username
     * @param $password
     * @return JsonResult
     */
    public function login($username, $password) {

        $data = ['username' => $username, 'password' => $password];
        return new JsonResult(200, 'login successfully', $data);

    }

    /**
     * @param $page
     * @param $pagesize
     * @return JsonResult
     */
    public function list($page, $pagesize) {
        $result = new JsonResult(200, 'SUCCESS');
        $result->putItems([
            array("username" => "zhangshan", "userid" => StringUtils::genGlobalUid()),
            array("username" => "xiaoming", "userid" => StringUtils::genGlobalUid())
        ]);
        $result->putData('page', $page);
        $result->putData('pagesize', $pagesize);
        return $result;
    }

}
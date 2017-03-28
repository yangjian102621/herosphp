<?php
namespace api\service;
use herosphp\api\interfaces\IRestfulApiService;
use herosphp\utils\JsonResult;

/**
 * restful api test
 * @author yangjian
 * @email yangjian102621@gmail.com
 * @date 2017-03-26
 */
class ShopService implements IRestfulApiService {

    public function add($data)
    {
        return new JsonResult(201, "添加数据成功", $data);
    }

    public function get($ID)
    {
        return new JsonResult(200, "查询成功", ['username' => 'xxxxxx', 'password' => '111111']);
    }

    public function gets($filter, $page, $pagesize, $sort)
    {
        return new JsonResult(200, "查询成功", [
            ['username' => 'xxxxxx', 'password' => '111111'],
            ['username' => 'yyyyyy', 'password' => '222222']
        ]);
    }

    public function update($ID, $data)
    {
        return new JsonResult(200, "更新数据成功", $data);
    }

    public function delete($ID)
    {
        return new JsonResult(204);
    }
}
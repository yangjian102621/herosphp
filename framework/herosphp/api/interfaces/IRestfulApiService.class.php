<?php
/**
 * restful api 通用接口.
 * @author yangjian
 * @date 2017-03-27
 */
namespace herosphp\api\interfaces;

use herosphp\utils\JsonResult;

interface  IRestfulApiService {

    /**
     * @param $data
     * @return JsonResult
     */
    public function add($data);

    /**
     * @param $ID
     * @return JsonResult
     */
    public function get($ID);

    /**
     * @param $filter
     * @param $page
     * @param $pagesize
     * @param $sort
     * @return JsonResult
     */
    public function gets($filter, $page, $pagesize, $sort);

    /**
     * @param $ID
     * @param $data
     * @return JsonResult
     */
    public function update($ID, $data);

    /**
     * @param $ID
     * @return JsonResult
     */
    public function delete($ID);
}
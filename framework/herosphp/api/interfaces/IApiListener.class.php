<?php
namespace herosphp\api\interfaces;
/**
 * api 访问拦截器接口.
 * @author yangjian
 * @date 2017-03-27
 */
interface  IApiListener {

    /**
     * @param $params
     * @return bool
     */
    public function authorize($params);
}
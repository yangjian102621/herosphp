<?php

namespace common\listener;

use herosphp\api\interfaces\IApiListener;

/**
 * api 服务拦截器
 * @package common\listener
 * @author yangjian102621@gmail.com
 */
 class ApiListener implements IApiListener {

     /**
      * @param $params
      * @return bool
      */
     public function authorize($params)
     {
         //这里写授权认证的代码
         return true;
     }
 }

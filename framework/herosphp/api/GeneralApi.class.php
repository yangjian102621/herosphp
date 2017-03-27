<?php

namespace herosphp\api;
use herosphp\bean\Beans;
use herosphp\core\Log;
use herosphp\utils\JsonResult;

/**
 * 普通 api 网关
 * Class GeneralApi
 * @package herosphp\api
 */

class GeneralApi {

    /**
     * 调用服务
     */
    public static function run() {
        $instance = new self();
        $urlParams = $instance->_getUrlParams();

        $service = Beans::get("api.".substr($urlParams[0], 0, strlen($urlParams[0])-1).".service");
        if ( is_object($service) ) {
            try {
                $reflectMethods = new \ReflectionMethod($service, $urlParams[1]);
                $params = $_GET + $_POST; //获取参数
                $dependParams = array(); //依赖参数
                foreach ($reflectMethods->getParameters() as $value) {
                    if (isset($params[$value->getName()])) {
                        $dependParams[] = $params[$value->getName()];
                    } else if ($value->isDefaultValueAvailable()) {
                        $dependParams[] = $value->getDefaultValue();
                    } else {
                        $dependParams[] = null;
                    }
                }
                $result = call_user_func_array(array($service, $urlParams[1]), $dependParams);
                $result->output();
            } catch (\Exception $e) {
                Log::error($e);
                JsonResult::jsonResult(201, $e->getMessage());
            }
        }
        JsonResult::jsonResult(404, "调用服务失败,找不到对应的服务.");
    }

    /**
     * 解析URL，提取url参数
     */
    private function _getUrlParams() {
        $pathInfo = parse_url($_SERVER['REQUEST_URI']);
        $params = explode('/', trim($pathInfo['path'], '/'));

        if ( empty($params[1]) ) {
            JsonResult::jsonResult(404);
        }
        return $params;
    }
} 
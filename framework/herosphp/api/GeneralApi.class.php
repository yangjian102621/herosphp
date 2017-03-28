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

        try {
            $instance = new self();
            $urlParams = $instance->_getUrlParams();
            $instance->_invoke($urlParams);
        } catch (\Exception $e) {
            Log::error($e); //记录日志
            JsonResult::jsonResult($e->getCode(), $e->getMessage());
        }

    }

    /**
     * 服务调用
     * @param $urlParams array 访问url路径中提取的参数
     * @throws APIException
     */
    private function _invoke($urlParams) {

        $serviceBean = "api.".substr($urlParams[0], 0, strlen($urlParams[0])-1).".service";
        $service = Beans::get($serviceBean);
        if ( is_null($service) || !is_object($service) ) {
            throw new APIException(404, "Can not find the servive '{$serviceBean}'.");
        }

        $params = $_GET + $_POST; //获取参数
        //这里做拦截和权限认证操作
        $listener = Beans::get(Beans::BEAN_API_LISTENER);
        if ( is_object($listener) && method_exists($listener, 'authorize') ) {
            if ( !$listener->authorize($params) ) {
                throw new APIException(401, "Authorized Faild.");
            }
        }

        try {
            $reflectMethods = new \ReflectionMethod($service, $urlParams[1]);
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
        } catch (\Exception $e) {
            throw new APIException(500, $e->getMessage());
        }
        $result->output();  //输出json数据
    }

    /**
     * 解析URL，提取url参数
     */
    private function _getUrlParams() {
        $pathInfo = parse_url($_SERVER['REQUEST_URI']);
        $params = explode('/', trim($pathInfo['path'], '/'));

        if ( empty($params[1]) ) {
            throw new APIException(404, 'Invalid resource path.');
        }
        return $params;
    }
} 
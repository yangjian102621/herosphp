<?php
namespace api\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\core\Log;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;
use herosphp\web\WebUtils;
use rsa\RSACrypt;
use rsa\SignUtil;

/**
 * API路由控制器
 * @author          yangjian<yangjian102621@gmail.com>
 */
class RouterAction extends Controller
{

    const SIGN_ERROR = '签名错误';
    const SIGN_EXPIRED = '签名过期';
    const TIME_STAMP = 30; //签名时间戳
    const VALIDATE_SIGN = false;//是否验证签名
    const VALIDATE_TOKEN = true;//是否验证签名
    const VALIDATE_TIME = false;//是否验证超时

    const VALIDATE_IP_WHITE_LIST = false;//是否验证超时

    const TOKEN='bf5c1dd77201cd0d046c115f97feee2b';

    public function index(HttpRequest $request)
    {

        //只允许post请求访问
        /*if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
            AjaxResult::ajaxResult('400', 'Please send a request using the POST method.');
        }*/

        //$this->checkPermission($request);

        $request->parseURL(); //重新解析，获取原来的url
        $service = $request->getAction();
        $method = $request->getMethod();

        $instance = Beans::get("api.{$service}.service");
        if (is_object($instance)) {
            $reflectMethods = new \ReflectionMethod($instance, $method);
            $params = $request->getParameters(); //获取外部POST过来的参数
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
            try {
                $result = call_user_func_array(array($instance, $method), $dependParams);
            } catch (\Exception $e) {
                Log::error($e);
            }
            die($result->output());
        }
        JsonResult::jsonResult(404, "调用服务失败,找不到对应的服务.");
    }

    //检查权限
    protected function checkPermission(HttpRequest $request)
    {

        $token = getHttpHeader('token');
        $sign = $request->getParameter('__sign');
        $timer = $request->getParameter('__timer', 'intval');

        Log::info('token :'.$token);

        //1. 验证签名
        if (self::VALIDATE_SIGN) {

            $rsa = new RSACrypt();
            $sign = $rsa->decryptByPrivateKey($sign);
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $request->getRequestUri();
            $_sign = SignUtil::sign($url, $request->getParameters());
            if ($sign != $_sign) {
                AjaxResult::ajaxResult('201', self::SIGN_ERROR);
            }

        }

        //2. 验证token
        if (self::VALIDATE_TOKEN) {

            if ($token != self::TOKEN) {
                AjaxResult::ajaxResult('203', "The token is invalid.");
            }
        }

        //3. 验证是否超时
        if (self::VALIDATE_TIME) {

            if (time() - $timer > self::TIME_STAMP) {
                AjaxResult::ajaxResult('202', self::SIGN_EXPIRED);
            }
        }

        //4. 验证ip白名单
        if (self::VALIDATE_IP_WHITE_LIST) {
            $service = Beans::get('system.setting.service');
            $ipList = $service->getSetting('ip_white_list');
            if (trim($ipList) != '') {
                $ipList = explode(',', $ipList);
                $ip = WebUtils::getClientIP();
                if (!in_array($ip, $ipList)) {
                    AjaxResult::ajaxResult('110', "Warning, you are taking a hacker attempt.");
                }
            }

        }


    }


}
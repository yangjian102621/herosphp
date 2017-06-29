<?php
/**
 * Beans 创建工具类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\bean;

use herosphp\core\Loader;
use \ReflectionClass;

class BeanUtil {

    /**
     * 创建Bean实例
     * @param string $classPath 要创建的对象的类路径
     * @param array $params 参数列表，可以是数组或者单个参数
     * @return object|ReflectionClass
     * @throws \Exception
     */
    public static function builtInstance( $classPath, $params=null ) {

        if( !is_string($classPath) ) return null;
        try{
            $importPath = str_replace('\\','.', $classPath);
            Loader::import($importPath, IMPORT_APP, EXT_PHP);
            $instance = new ReflectionClass($classPath);
            if( is_array($params) ){
                return $instance->newInstanceArgs($params);
            } elseif ( $params ) {
                return $instance->newInstance($params);
            } else {
                return $instance->newInstance();
            }
        } catch ( \Exception $e ) {
            E($e->getMessage());
        }
    }

    /**
     * 给对象装配属性，如果对象不存在则创建对象
     * @param string | object $clazz
     * @param $properties
     * @return object
     * @throws BeanException
     */
    public static function install( $clazz, $properties ){
        if( !$properties ) return;
        //字符串类路径
        if( is_string($clazz) ) {
            $refClass =new ReflectionClass($clazz);
            $obj = $refClass->newInstance();
        } else {    //安装对象
            $obj = $clazz;
            $refClass =new ReflectionClass($obj);
        }
        $methodName = NULL;
        $method = NULL;
        foreach($properties as $key => $value){
            $methodName = "set" . ucwords($key);
            if( $refClass->hasMethod($methodName )){
                $method = $refClass->getMethod($methodName);
                try {
                    $method->invoke($obj, $properties[$key]);
                } catch (\Exception $e) {
                    throw new BeanException($e->getMessage());
                }
            }
        }
        return $obj;
    }

    /**
     * 检查类是否有指定方法
     * @param mixed $clazz
     * @param string $methodName
     * @return 如果有方法，返回true
     */
    public static function hasMethod($clazz, $methodName){
        if(! $clazz instanceof ReflectionClass){
            $clazz = new ReflectionClass($clazz);
        }
        return $clazz->hasMethod($methodName);
    }

    /**
     * 调用对象的方法，返回方法返回的值
     * @param object $object 被调用的对象
     * @param string $methodName 方法名
     * @param mixed $params 参数
     *  @throws BeanException
     * @return mixed 返回被调用方法的返回值
     */
    public static function invokeMethod( $object, $methodName, $params ){
        $clazz =new ReflectionClass($object);
        $method = $clazz->getMethod($methodName);
        if( !$method ) return null;
        try {
            if( is_array($params) ){
                return $method->invokeArgs($object, $params);
            } else {
                return $method->invoke($object, $params);
            }
        } catch (\Exception $e) {
            throw new BeanException($e->getMessage());
        }
    }
}

<?php
/*---------------------------------------------------------------------
 * 创建&管理Bean对象
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\bean;
use herosphp\core\Loader;
use herosphp\exception\BeanException;

Loader::import('exception.BeanException', IMPORT_FRAME);
Loader::import('bean.BeanUtil', IMPORT_FRAME);
abstract class Beans {

    /**
     * Bean的类型:对象
     * @var string
     */
    const BEAN_OBJECT = 'OBJECT';

    /**
     * Bean的类型:对象数组
     * @var string
     */
    const BEAN_OBJECT_ARRAY = 'OB_ARRAY';

    /**
     * 应用程序监听器bean key
     * @var string
     */
    const BEAN_WEBAPP_LISTENER = 'webapplication.listener.bean';

    /**
     * Bean装配配置信息
     * @var array
     */
    private static $CONFIGS = array();

    /**
     * Bean缓存池
     * @var array
     */
    private static $BEAN_POOL = array();

    /**
     * 获取指定ID的Bean
     * @param string $key Bean的key
     * @param boolean $new 是否创建新的bean,新创建的Bean不会放到容器中，如果要放到容器中，请使用set方法。
     * @return Object 返回指定ID的Bean,如果Bean不存在则返回null
     */
    public static function get( $key, $new=false ){

        if ( empty(self::$CONFIGS) ) {
            self::$CONFIGS = Loader::config('*', 'beans');
            if ( !self::$CONFIGS ) return null;
        }
        $beanConfig = self::$CONFIGS[$key];
        if( $new || $beanConfig['@single'] === false ){
            return self::build($beanConfig);
        }
        if( !isset(self::$BEAN_POOL[$key]) ){
            self::$BEAN_POOL[$key] = self::build($beanConfig);
        }
        return self::$BEAN_POOL[$key];
    }

    /**
     * 删除指定ID的Bean并返回被删除的Bean
     * @param string $key
     */
    public static function delete( $key ){
        self::$BEAN_POOL[$key] = null;  //释放bean占用的内存
    }

    /**
     * @param array $configs
     */
    public static function setConfigs( &$configs = null ){
        if ( $configs ) self::$CONFIGS = $configs;
    }

    /**
     * @param $key
     * @param $config
     */
    public static function addConfigs( $key, $config ) {
        if ( $config ) self::$CONFIGS[$key] = $config;
    }

    /**
     * 根据Bean装配配置来创建Bean
     * @param array $beanConfig Bean装配配置
     * @return mixed 返回创建的Bean
     */
    private static function build( $beanConfig ) {

        if( is_array($beanConfig) && $beanConfig['@type'] ){
            switch ( $beanConfig['@type'] ) {
                //单个对象
                case Beans::BEAN_OBJECT:
                    $bean = BeanUtil::builtInstance($beanConfig['@class'], $beanConfig['@params']);
                    //属性装载
                    $attributes = $beanConfig['@attributes'];
                    if ( $attributes ) {
                        self::attributesInstall($bean, $attributes);
                    }
                    //方法调用
                    $invokes = $beanConfig['@invokes'];
                    if ( $invokes ) {
                        self::methodsInvoke($bean, $invokes);
                    }
                    return $bean;

                //对象数组
                case Beans::BEAN_OBJECT_ARRAY:
                    $beans = array();
                    $bean = null;
                    foreach($beanConfig['@attributes'] as $beanKey=>$subBeanConfig){
                        $bean = self::build($subBeanConfig);
                        $beans[$beanKey] = $bean;
                    }
                    return $beans;
            }
        }
        return null;
    }

    /**
     * 根据属性配置装配Bean属性
     * @param Object $bean Bean对象
     * @param array $attributes 属性配置
     * @throws \herosphp\exception\BeanException
     */
    private static function attributesInstall($bean, $attributes){
        $attributesToInstall = array();	//用来存放处理过后的属性
        foreach($attributes as $attributeName=>$attributeValue){
            if($attributeName[0] == '@'){
                //以@开头的属性名为需要进一步处理的属性
                $attributeNames = explode('/', $attributeName);
                if(count($attributeNames) != 2) continue;
                $attributeName = $attributeNames[1];
                switch($attributeNames[0]){
                    case '@id':
                        //属性值是一个id指定的Bean
                        $attributeValue = self::get($attributeValue);
                        break;
                    case '@bean':
                        //属性值是一个@bean配置项目，需要创建这个Bean
                        if( !is_array($attributeValue) ) {
                            $exception = new BeanException("Bean属性配置错误!");
                            $exception->setBean($bean);
                            $exception->setAttributes($attributeValue);
                            throw $exception;
                        }
                        $attributeValue = self::build($attributeValue);
                        break;
                    default:
                        continue;
                        break;
                }
            }
            $attributesToInstall[$attributeName] = $attributeValue;
        }
        BeanUtil::install($bean, $attributesToInstall);
    }

    /**
     * 根据方法调用配置调用指定Bean的方法
     * @param Object $bean Bean对象
     * @param array $invokes 方法调用配置
     * @throws \herosphp\exception\BeanException
     */
    private static function methodsInvoke($bean, $invokes){
        foreach( $invokes as $method=>$params ) {
            if( is_int($method) ) {
                $method = $params;
                $params = null;
            } elseif ( $method[0] == '@' ) {
                //以@开头的方法名为需要进一步处理的方法
                $methods = explode('/', $method);
                if(count($methods) != 2) continue;
                $method = $methods[1];
                switch( $methods[0] ) {
                    case '@id':
                        //参数是一个id指定的Bean
                        $params = Beans::get($params);
                        break;
                    case '@bean':
                        //参数是一个@bean配置项目，需要创建这个Bean
                        if( !is_array($params) ){
                            $exception = new BeanException("Bean属性配置错误!");
                            $exception->setBean($bean);
                            $exception->setAttributes($params);
                            throw $exception;
                        }
                        $params = Beans::build($params);
                        break;
                    default:
                        continue;
                        break;
                }
            }
            BeanUtil::invokeMethod($bean, $method, $params);
        }
    }
}
?>

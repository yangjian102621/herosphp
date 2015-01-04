<?php
/*---------------------------------------------------------------------
 * Bean属性配置异常
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\exception;

class BeanException extends HeroException{

    /**
     * Bean
     * @var
     */
    private $bean;

    /**
     * 属性
     * @var mixed
     */
    private $attributes;

    /**
     * 调用的方法
     * @var string
     */
    private $method;

    function __construct( $message, $code ){
        parent::__construct( $message, $code );
    }

    /**
     * @return \herosphp\bean\Beans
     */
    public function getBean() {
        return $this->bean;
    }

    /**
     * @return the $attributes
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @param \herosphp\bean\Beans $bean
     */
    public function setBean($bean) {
        $this->bean = $bean;
    }

    /**
     * @param  $attributes
     */
    public function setAttributes($attributes) {
        $this->attributes = $attributes;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: yangjian
 * Date: 16-3-17
 * Time: 下午10:12
 */

class Test extends PHPUnit_Framework_TestCase
{

    public function testArray()
    {
        $stack = array();
        $this->assertEquals(0, count($stack));

        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertEquals(1, count($stack));

        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0, count($stack));
    }
    /**
     * @test
     */
    public function Stringlen()
    {
        $str = 'abc';
        $this->assertEquals(3, strlen($str));
    }
}
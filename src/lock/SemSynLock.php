<?php
/**
 * 同步锁，通过系统信号量加锁方式实现
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2016-12-07 v2.0.0
 */
namespace herosphp\lock;

use herosphp\core\Loader;
use herosphp\lock\interfaces\ISynLock;

class SemSynLock implements ISynLock {

    private $ipc_signal = null; //系统信号量

    public function __construct($key)
    {
        if ( !function_exists('sem_acquire') ) {
            E("您的系统不支持信号量操作，请先安装 pcntl 扩展");
        }
        if ( !is_long($key) ) E("传入了非法的信号量key");
        $this->ipc_signal = sem_get($key);
    }

    /**
     * 尝试去获取锁，成功返回false并且一直阻塞
     * @throws \herosphp\exception\HeroException
     */
    public function tryLock()
    {
        if ( sem_acquire($this->ipc_signal) === false ) {
            E('获取信号量锁失败，锁定失败.');
            return false;
        }
        return true;
    }

    /**
     * 释放锁
     * @throws \herosphp\exception\HeroException
     */
    public function unlock()
    {
        if ( sem_release($this->ipc_signal) === false ) {
            E('释放信号量锁失败.');
            return false;
        }
        return true;
    }
}
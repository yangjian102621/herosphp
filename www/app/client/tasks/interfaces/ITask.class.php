<?php
/**
 * 客户端任务接口
 * @author yangjian102621@gmail.com
 * @version 1.0.0
 */

namespace tasks\interfaces;


interface ITask {

    /**
     * 运行任务
     * @return mixed
     */
    public function run();
} 
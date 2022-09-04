<?php

declare(strict_types=1);
/**
 * This file is part of Heros-Worker.
 *
 * @contact  chenzf@pvc123.com
 */

namespace process;

use Exception;
use herosCron\Crontab;
use herosphp\utils\Lock;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Worker;

/**
 * 采用异步通知消息来完成定时任务
 * 主要解决是如果定时任务定义间隙过短、任务执行过久，导致部分任务跳过。
 * 参考[http://doc.workerman.net/faq/async-task.html].
 */
class CrontabWorker
{


    // 定时任务列表 memo:定时任务的备注，该属性为可选属性，没有任何逻辑上的意义，仅供开发人员查阅帮助对该定时任务的理解。
    protected static array $cronList = [
       [
           'rule' => '* * * * * *',  //支持秒
           'task' => [AsyncTask::class, 'run'],  //处理类和执行方法
           'memo' => 'say Hello'  //备注可选
       ]
    ];

    /**
     * @param Worker $worker
     * @return void
     */
    public function onWorkerStart(Worker $worker): void
    {
        foreach (static::$cronList ?? [] as $cron) {
            new Crontab($cron['rule'], static function () use ($cron) {
                static::delivery($cron['task'][0], $cron['task'][1], $cron['memo']);
            });
        }
    }

    /**
     * 投递到异步进程. 一个定时任务执行比较久，间隔设置时间比较短，加锁。一个任务一个时刻仅有一个运行.
     *
     * @throws Exception
     */
    private static function delivery(string $clazz, string $method, string $memo): void
    {
        $lock = Lock::get("{$clazz}{$method}");
        if ($lock->tryLock()) {
            $taskConnection = new AsyncTcpConnection('tcp://127.0.0.1:8182');
            $taskConnection->send(json_encode(['clazz' => $clazz, 'method' => $method]));
            $taskConnection->onMessage = function (AsyncTcpConnection $asyncTcpConnection, $taskResult) use ($lock) {
                $asyncTcpConnection->close();
                $lock->unlock();
            };
            $taskConnection->connect();
        }


    }
}

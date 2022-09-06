<?php
declare(strict_types=1);

namespace app\queue;

use herosRQueue\ConsumerInterface;

class DemoQueue implements ConsumerInterface
{
    //要消费的队列名
    public string $queue = 'demo';

    // 连接名，对应 config/redis_queue.php 里的连接`
    public string $connection = 'default';

    public function consume(array $data): void
    {
        echo 'queue'.json_encode($data).PHP_EOL;
    }
}

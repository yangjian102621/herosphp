<?php
declare(strict_types=1);
namespace herosRQueue;

use Workerman\Worker;

class Consumer
{
    /**
     * @var string
     */
    protected string $consumerDir;

    /**
     * StompConsumer constructor.
     */
    public function __construct(string $consumerDir)
    {
        $this->consumerDir = $consumerDir;
    }

    /**
     * onWorkerStart.
     */
    public function onWorkerStart(Worker $worker)
    {
        if (file_exists($this->consumerDir)) {
            //todo: 优化遍历文件
            $dirIterator = new \RecursiveDirectoryIterator($this->consumerDir);
            $iterator = new \RecursiveIteratorIterator($dirIterator);
            /** @var \SplFileInfo $file */
            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    continue;
                }
                $ext = $file->getExtension();
                if ('php' === $ext) {
                    $class = '\\'.str_replace('/', '\\', substr(substr($file->getPath(), strlen(BASE_PATH)), 0)).'\\'.substr($file->getFilename(), 0, -4);
                    if (! class_exists($class)) {
                        printf("%s \033[31m\033[1m[ERROR]\033[0m %s\n", date('Y-m-d H:i:s'), "{$class} not exist!");
                        continue;
                    }
                    $consumer = new $class;
                    $connectionName = $consumer->connection ?? 'default';
                    $queue = $consumer->queue;
                    $connection = Client::connection($connectionName);
                    $connection->subscribe($queue, [$consumer, 'consume']);
                }
            }
        }
    }
}

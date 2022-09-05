<?php
declare(strict_types=1);

namespace herosEvent;

class EventStarter
{
    protected static bool $debug = false;

    /**
     * @var array
     */
    protected static array $events = [];

    /**
     * @throws \ReflectionException
     */
    public static function init(array $config = []): void
    {
        static::getEvents($config);
        if (static::$debug) {
            $table = new Table();
            $table->setCellStyle(Table::COLOR_GREEN);
            foreach (Event::list() as $id => $item) {
                $eventName = $item[0];
                $callback = $item[1];
                if (is_array($callback) && is_object($callback[0])) {
                    $callback[0] = get_class($callback[0]);
                }
                $cb = $callback instanceof \Closure ? 'Closure' : (is_array($callback) ? json_encode($callback) : var_export($callback, true));
                $table->row([
                    'id' => $id,
                    'event_name' => $eventName,
                    'callback' => $cb,
                ]);
            }
            echo $table.PHP_EOL;
        }
    }

    /**
     * @throws \ReflectionException
     */
    protected static function convertCallable($callback)
    {
        if (\is_array($callback)) {
            $callback = \array_values($callback);
            if (isset($callback[1]) && \is_string($callback[0]) && \class_exists($callback[0])) {
                $rm = new \ReflectionMethod($callback[0], $callback[1]);
                if ($rm->isStatic()) {
                    $callback = [$callback[0], $callback[1]];
                } else {
                    $callback = [(new \ReflectionClass($callback[0]))->newInstance(), $callback[1]];
                }
            }
        }
        return $callback;
    }

    /**
     * @param array $configs
     * @return void
     * @throws \ReflectionException
     */
    protected static function getEvents(array $configs)
    {
        $events = [];
        foreach ($configs as $eventName => $callbacks) {
            $callbacks = static::convertCallable($callbacks);
            if (is_callable($callbacks)) {
                $events[$eventName] = [$callbacks];
                Event::on($eventName, $callbacks);
                continue;
            }
            if (! is_array($callbacks)) {
                $msg = "Events: $eventName => ".var_export($callbacks, true)." is not callable\n";
                echo $msg;
                continue;
            }
            foreach ($callbacks as $callback) {
                $callback = static::convertCallable($callback);
                if (is_callable($callback)) {
                    $events[$eventName][] = $callback;
                    Event::on($eventName, $callback);
                    continue;
                }
                $msg = "Events: $eventName => ".var_export($callback, true)." is not callable\n";
                echo $msg;
            }
        }
        static::$events = array_merge_recursive(static::$events, $events);
    }
}

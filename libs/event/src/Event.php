<?php
declare(strict_types=1);
namespace herosEvent;

class Event
{
    /**
     * @var array
     */
    protected static array $eventMap = [];

    /**
     * @var array
     */
    protected static array $prefixEventMap = [];

    /**
     * @var int
     */
    protected static int $id = 0;

    /**
     * @param $eventName
     * @param  callable  $callback
     * @return int
     */
    public static function on($eventName, callable $callback): int
    {
        $isPrefixName = $eventName[strlen($eventName) - 1] === '*';
        if ($isPrefixName) {
            static::$prefixEventMap[substr($eventName, 0, -1)][++static::$id] = $callback;
        } else {
            static::$eventMap[$eventName][++static::$id] = $callback;
        }

        return static::$id;
    }

    /**
     * @param $eventName
     * @param  int  $id
     * @return int
     */
    public static function off($eventName, int $id): int
    {
        if (isset(static::$eventMap[$eventName][$id])) {
            unset(static::$eventMap[$eventName][$id]);

            return 1;
        }

        return 0;
    }

    /**
     * @param $eventName
     * @param $data
     * @return int
     */
    public static function emit($eventName, $data): int
    {
        $successCount = 0;
        $callbacks = static::$eventMap[$eventName] ?? [];
        foreach (static::$prefixEventMap as $name => $callback_items) {
            if (str_starts_with($eventName, $name)) {
                $callbacks = array_merge($callbacks, $callback_items);
            }
        }
        ksort($callbacks);
        foreach ($callbacks as $callback) {
            try {
                $ret = $callback($data, $eventName);
                $successCount++;
            } catch (\Throwable $e) {
                printf("%s \033[31m\033[1m[ERROR][EVENT]\033[0m %s\n", date('Y-m-d H:i:s'), (string)$e);
                continue;
            }
            if ($ret === false) {
                return $successCount;
            }
        }
        return $successCount;
    }

    /**
     * @return array
     */
    public static function list(): array
    {
        $callbacks = [];
        foreach (static::$eventMap as $eventName => $callback_items) {
            foreach ($callback_items as $id => $callback_item) {
                $callbacks[$id] = [$eventName, $callback_item];
            }
        }
        foreach (static::$prefixEventMap as $eventName => $callback_items) {
            foreach ($callback_items as $id => $callback_item) {
                $callbacks[$id] = [$eventName.'*', $callback_item];
            }
        }
        ksort($callbacks);
        return $callbacks;
    }
}

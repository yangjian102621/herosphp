<?php

namespace herosphp\utils;

/**
 * simple单例Trait
 */
trait InstanceTrait
{
    protected static array $_instances = [];

    protected string $instanceKey;

    public static function getInstance(bool $refresh = false): static
    {
        $key = static::class;
        if (! $refresh && isset(static::$_instances[$key]) && static::$_instances[$key] instanceof static) {
            return static::$_instances[$key];
        }
        $client = new static();
        $client->instanceKey = $key;
        return static::$_instances[$key] = $client;
    }

    /**
     * @desc   回收单例对象
     *
     * @author limx
     */
    public function flushInstance(): void
    {
        unset(self::$_instances[$this->instanceKey]);
    }
}

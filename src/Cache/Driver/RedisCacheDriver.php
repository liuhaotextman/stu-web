<?php

namespace Snow\StuWeb\Cache\Driver;

use Snow\StuWeb\Contracts\Cache\CacheDriverInterface;
use Snow\StuWeb\Redis\RedisManager;

class RedisCacheDriver implements CacheDriverInterface
{
    /**
     * @var RedisManager
     */
    protected $handler;

    public function __construct(RedisManager $redisManager)
    {
        $this->handler = $redisManager;
    }

    public function get(string $key)
    {
        return $this->handler->get($key);
    }

    public function set(string $key, $value, int $expire = 0)
    {
        if ($expire) {
            return $this->handler->setex($key, $expire, $value);
        }
        return $this->handler->set($key, $value);
    }
}
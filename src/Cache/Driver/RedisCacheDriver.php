<?php

namespace Snow\StuWeb\Cache\Driver;

use Snow\StuWeb\Contracts\Cache\CacheDriverInterface;
use Snow\StuWeb\Redis\RedisManager;

class RedisCacheDriver implements CacheDriverInterface
{
    /**
     * @var \Redis
     */
    protected $handler;

    public function __construct(RedisManager $redisManager)
    {
        $this->handler = $redisManager->getManager();
    }

    public function get(string $key)
    {
        return $this->handler->get($key);
    }

    public function set(string $key, $value, int $expire = 0)
    {
        if ($expire) {
            return $this->handler->setex($key, $value, $expire);
        }
        return $this->handler->set($key, $value);
    }
}
<?php

namespace Snow\StuWeb\Redis;

use Redis;
use Snow\StuWeb\Http\App;

class RedisManager
{
    protected App $app;

    /**
     * @var Redis
     */
    protected $handler;

    public function __construct(App $app)
    {
        $this->app = $app;
        $redisConfigFile = $app->getConfigPath() . DIRECTORY_SEPARATOR . 'redis.php';
        $config = require $redisConfigFile;

        $redis = new Redis();
        $redis->connect($config['host'], $config['port']);
        if (isset($config['auth']) && $config['auth']) {
            $redis->auth($config['auth']);
        }

        $this->handler = $redis;
    }

    public function getManager(): Redis
    {
        return $this->handler;
    }
}
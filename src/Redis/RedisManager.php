<?php

namespace Snow\StuWeb\Redis;

use Redis;
use Snow\StuWeb\Http\App;

class RedisManager extends Redis
{
    protected App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        $redisConfigFile = $app->getConfigPath() . DIRECTORY_SEPARATOR . 'redis.php';
        $config = require $redisConfigFile;

        $this->connect($config['host'], $config['port']);
        if (isset($config['auth']) && $config['auth']) {
            $this->auth($config['auth']);
        }
        parent::__construct();
    }
}
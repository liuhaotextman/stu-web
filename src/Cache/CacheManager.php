<?php

namespace Snow\StuWeb\Cache;

use Snow\StuWeb\Cache\Driver\FileCacheDriver;
use Snow\StuWeb\Cache\Driver\RedisCacheDriver;
use Snow\StuWeb\Contracts\Cache\CacheDriverInterface;
use Snow\StuWeb\Exception\CacheException;
use Snow\StuWeb\Http\App;

class CacheManager implements CacheDriverInterface
{
    protected App $app;

    /**
     * @var CacheDriverInterface
     */
    protected $driver;

    protected array $driverMaps = [
        'file' => FileCacheDriver::class,
        'redis' => RedisCacheDriver::class
    ];

    public function __construct(App $app)
    {
        $this->app = $app;
    }


    protected function getDrive(): CacheDriverInterface
    {
        if (!$this->driver) {
            $cacheConfigFile = $this->app->getConfigPath() . DIRECTORY_SEPARATOR . 'cache.php';
            if (!file_exists($cacheConfigFile)) {
                throw new CacheException('cache config file ' . $cacheConfigFile . 'not exists');
            }
            $cacheConfig = require $cacheConfigFile;
            $type = $cacheConfig['type'] ?? 'file';
            if (!isset($this->driverMaps[$type])) {
                throw new CacheException('cache driver type' . $type . 'not exists');
            }
            $driverName = $this->driverMaps[$type];
            $this->driver = $this->app->make($driverName);
        }

        return $this->driver;
    }

    public function get(string $key)
    {
        return $this->getDrive()->get($key);
    }

    public function set(string $key, $value, $expire = 0)
    {
        return $this->getDrive()->set($key, $value, $expire);
    }
}
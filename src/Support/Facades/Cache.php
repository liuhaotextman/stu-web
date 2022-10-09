<?php

namespace Snow\StuWeb\Support\Facades;

use Snow\StuWeb\Cache\CacheManager;
use Snow\StuWeb\Support\Facade;

/**
 * @method static string|bool get(string $key)
 * @method static bool set(string $key, string $value, int $expire = 0)
 */
class Cache extends Facade
{
    protected static function getFacadeClass(): string
    {
        return CacheManager::class;
    }
}
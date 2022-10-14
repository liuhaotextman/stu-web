<?php

namespace Snow\StuWeb\Support\Facades;

use Snow\StuWeb\Redis\RedisManager;
use Snow\StuWeb\Support\Facade;

/**
 * @see RedisManager
 */
class Redis extends Facade
{
    protected static function getFacadeClass(): string
    {
        return RedisManager::class;
    }
}
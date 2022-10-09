<?php

namespace Snow\StuWeb\Contracts\Cache;

interface CacheDriverInterface
{
    public function get(string $key);

    public function set(string $key, $value, int $expire = 0);
}
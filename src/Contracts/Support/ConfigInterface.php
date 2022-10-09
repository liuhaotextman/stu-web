<?php

namespace Snow\StuWeb\Contracts\Support;

interface ConfigInterface
{
    public function load(string $configFile): array;

    public function get(string $key, $default = null);
}

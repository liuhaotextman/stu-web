<?php

namespace Snow\StuWeb\Contracts\Support;

interface FacadeInterface
{
    public static function __callStatic($method, $params);
}
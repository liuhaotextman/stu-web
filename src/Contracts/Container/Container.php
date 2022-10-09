<?php

namespace Snow\StuWeb\Contracts\Container;

use Closure;
use Psr\Container\ContainerInterface;

interface Container extends ContainerInterface
{
    public function make(string $abstract, array $vars = [], bool $newInstance = false);

    public function bind(string $abstract, $concrete);

    public function instance(string $abstract, $instance);

    public function invokeFunction($function, array $vars = []);

    public function invokeMethod($method, array $vars = [], bool $accessible = false);

    public function invoke($callable, array $vars = [], bool $accessible = false);

    public function invokeClass(string $class, array $vars = []);

    public function bindArr(array $arr);
}
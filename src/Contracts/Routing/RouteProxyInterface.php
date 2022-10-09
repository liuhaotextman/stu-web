<?php

namespace Snow\StuWeb\Contracts\Routing;

use Closure;

interface RouteProxyInterface
{
    public function map(array $methods, string $uri, $action): RouteItemInterface;

    public function group(Closure $closure): RouteProxyInterface;

    public function middleware(string $middleware): RouteProxyInterface;

    public function getPrefix(): string;

    public function getMiddlewares(): array;
}
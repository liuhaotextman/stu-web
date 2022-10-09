<?php

namespace Snow\StuWeb\Support\Facades;

use Closure;
use Snow\StuWeb\Contracts\Routing\RouteInterface;
use Snow\StuWeb\Contracts\Routing\RouteItemInterface;
use Snow\StuWeb\Contracts\Routing\RouteProxyInterface;
use Snow\StuWeb\Support\Facade;

/**
 * @method static RouteItemInterface get(string $uri, $action)
 * @method static RouteItemInterface post(string $uri, $action)
 * @method static RouteItemInterface put(string $uri, $action)
 * @method static RouteItemInterface delete(string $uri, $action)
 * @method static RouteItemInterface any(string $uri, $action)
 * @method static RouteProxyInterface group(string $prefix, Closure $closure)
 */
class Route extends Facade
{
    protected static function getFacadeClass(): string
    {
        return RouteInterface::class;
    }
}

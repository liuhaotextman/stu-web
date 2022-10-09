<?php

namespace Snow\StuWeb\Contracts\Routing;

interface RouteInterface
{
    public function get(string $uri, $action): RouteItemInterface;

    public function post(string $uri, $action): RouteItemInterface;

    public function put(string $uri, $action): RouteItemInterface;

    public function delete(string $uri, $action): RouteItemInterface;

    public function any(string $uri, $action): RouteItemInterface;

    public function group(string $prefix, \Closure $closure): RouteProxyInterface;

    public function addRouteItem(array $methods, string $uri, $action): RouteItemInterface;

    /**
     * @return RouteItemInterface[]
     */
    public function getRouteItems(): array;
}
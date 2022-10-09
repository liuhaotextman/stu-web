<?php

namespace Snow\StuWeb\Routing;

use Closure;
use Snow\StuWeb\Contracts\Routing\RouteInterface;
use Snow\StuWeb\Contracts\Routing\RouteItemInterface;
use Snow\StuWeb\Contracts\Routing\RouteProxyInterface;

class RouteCollector implements RouteInterface
{
    protected static $instance;

    /**
     * @var RouteItemInterface[]
     */
    protected array $routeItems = [];

    /**
     * @var RouteProxyInterface[]
     */
    protected  array $routeProxy = [];

    public function get(string $uri, $action): RouteItemInterface
    {
        return $this->map(['GET'], $uri, $action);
    }

    public function post(string $uri, $action): RouteItemInterface
    {
        return $this->map(['POST'], $uri, $action);
    }

    public function put(string $uri, $action): RouteItemInterface
    {
        return $this->map(['PUT'], $uri, $action);
    }

    public function delete(string $uri, $action): RouteItemInterface
    {
        return $this->map(['DELETE'], $uri, $action);
    }

    public function any(string $uri, $action): RouteItemInterface
    {
        return $this->map(['GET', 'POST', 'PUT', 'DELETE'], $uri, $action);
    }

    protected function map(array $methods, string $uri, $action): RouteItemInterface
    {
        if (!$this->routeProxy) {
            array_push($this->routeProxy, new RouteProxy($this));
        }
        $routeProxy = end($this->routeProxy);
        reset($this->routeProxy);
        return $routeProxy->map($methods, $uri, $action);
    }

    public function group(string $prefix, Closure $closure): RouteProxyInterface
    {
        $lastRouteProxy = end($this->routeProxy);
        if ($lastRouteProxy) {
            $prefix = $lastRouteProxy->getPrefix() . '/' . trim($prefix, '/');
        }
        reset($this->routeProxy);
        $routeProxy =  new RouteProxy($this, $prefix);
        array_push($this->routeProxy, $routeProxy);
        $routeProxy->group($closure);
        array_pop($this->routeProxy);

        return $routeProxy;
    }

    public function addRouteItem(array $methods, string $uri, $action): RouteItemInterface
    {
        $routeItem = new RouteItem($methods, $uri, $action, $this->routeProxy);
        array_push($this->routeItems, $routeItem);
        return $routeItem;
    }

    /**
     * @return RouteItemInterface[]
     */
    public function getRouteItems(): array
    {
        return $this->routeItems;
    }
}
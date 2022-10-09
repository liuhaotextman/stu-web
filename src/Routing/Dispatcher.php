<?php

namespace Snow\StuWeb\Routing;

use Snow\StuWeb\Contracts\Routing\DispatcherInterface;
use Snow\StuWeb\Contracts\Routing\RouteInterface;
use Snow\StuWeb\Contracts\Routing\RouteItemInterface;
use Snow\StuWeb\Contracts\Routing\RouteResultInterface;
use Snow\StuWeb\Exception\RouteException;

class Dispatcher implements DispatcherInterface
{
    protected RouteInterface $route;

    public function __construct(RouteInterface $route)
    {
        $this->route = $route;
    }

    public function routeResult(string $method, string $uri): RouteResultInterface
    {
        $routeItem = $this->matchRouteItem($method, $uri);
        $middlewares = [];
        foreach ($routeItem->getGroups() as $group) {
            $middlewares = array_merge($middlewares, $group->getMiddlewares());
        }

        $middlewares = array_merge($middlewares, $routeItem->getMiddlewares());
        $callable = $routeItem->getCallable();
        return new RouteResult($callable, $middlewares);
    }

    protected function matchRouteItem(string $method, string $uri): RouteItemInterface
    {
        foreach ($this->route->getRouteItems() as $item) {
            if (in_array($method, $item->getMethods()) && $uri == $item->getPattern()) {
                return $item;
            }
        }

        throw new RouteException('404 no found', 404);
    }

}
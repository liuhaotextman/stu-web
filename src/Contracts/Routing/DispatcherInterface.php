<?php

namespace Snow\StuWeb\Contracts\Routing;

interface DispatcherInterface
{
    public function routeResult(string $method, string $uri): RouteResultInterface;
}
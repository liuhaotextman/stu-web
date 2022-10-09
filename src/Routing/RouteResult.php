<?php

namespace Snow\StuWeb\Routing;

use Snow\StuWeb\Contracts\Routing\RouteResultInterface;

class RouteResult implements RouteResultInterface
{
    protected $callable;

    protected array $middlewares = [];

    public function __construct($callable, $middlewares)
    {
        $this->callable = $callable;
        $this->middlewares = $middlewares;
    }

    public function getCallable()
    {
        return $this->callable;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
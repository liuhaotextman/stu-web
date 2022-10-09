<?php

namespace Snow\StuWeb\Routing;

use Snow\StuWeb\Contracts\Middleware\MiddlewareInterface;
use Snow\StuWeb\Contracts\Routing\RouteItemInterface;
use Snow\StuWeb\Contracts\Routing\RouteProxyInterface;
use Snow\StuWeb\Exception\MiddlewareException;

class RouteItem implements RouteItemInterface
{
    protected array $middlewares = [];

    protected string $pattern = '';

    protected array $methods = [];

    /**
     * @var RouteProxyInterface[]
     */
    protected array $groups = [];

    protected $callable;

    public function __construct(array $methods, string $pattern, $callable, array $groups)
    {
        $this->methods = $methods;
        $this->pattern = $pattern;
        $this->callable = $callable;
        $this->groups = $groups;
    }

    public function middleware(string $middleware): RouteItemInterface
    {
        if (!class_exists($middleware)) {
            throw new MiddlewareException(sprintf('middleware %s class not exits', $middleware));
        }
        $interfaces = class_implements($middleware);
        if (!in_array(MiddlewareInterface::class, $interfaces)) {
            throw new MiddlewareException(sprintf('middleware %s must implement %s', $middleware, MiddlewareInterface::class));
        }
        array_push($this->middlewares, $middleware);
        return $this;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @return array|RouteProxyInterface[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
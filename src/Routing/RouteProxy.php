<?php

namespace Snow\StuWeb\Routing;

use Closure;
use Snow\StuWeb\Contracts\Middleware\MiddlewareInterface;
use Snow\StuWeb\Contracts\Routing\RouteInterface;
use Snow\StuWeb\Contracts\Routing\RouteItemInterface;
use Snow\StuWeb\Contracts\Routing\RouteProxyInterface;
use Snow\StuWeb\Exception\MiddlewareException;
use Snow\StuWeb\Exception\RouteException;

class RouteProxy implements RouteProxyInterface
{
    protected RouteInterface $route;

    protected string $prefix = '';

    protected array $middlewares = [];

    public function __construct(RouteInterface $route, string $prefix = '')
    {
        $this->route = $route;
        $this->prefix = rtrim($prefix, '/');
    }

    public function map(array $methods, string $uri, $action): RouteItemInterface
    {
        if (!is_array($action) && !($action instanceof Closure)) {
            throw new RouteException('action type error, action type must to be array or closure', 403);
        }

        if (is_array($action)) {
            list($controller, $function) = $action;
            if (!class_exists($controller)) {
                throw new RouteException(sprintf('controller %s is not exist', $controller), 401);
            }
            if (!method_exists($controller, $function)) {
                throw new RouteException(sprintf('action %s is not exist in %s controller', $action, $controller), 402);
            }
        }

        $uriFullPath = $this->prefix . '/' . trim($uri, '/');
        return $this->route->addRouteItem($methods, $uriFullPath, $action);
    }

    public function group(Closure $closure): RouteProxy
    {
        call_user_func($closure);
        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function middleware(string $middleware): RouteProxyInterface
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

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
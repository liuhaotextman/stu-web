<?php

namespace Snow\StuWeb\Middleware;

use Snow\StuWeb\Contracts\Middleware\MiddlewareManagerInterface;
use Snow\StuWeb\Http\App;

class MiddlewareManager implements MiddlewareManagerInterface
{
    protected App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    protected function middlewareCallables(array $middlewares)
    {
        return array_map(function ($middleware) {
            return function ($request, $next) use ($middleware) {
                $middlewareObj = $this->app->make($middleware);
                return $middlewareObj->handle($request, $next);
            };
        }, $middlewares);
    }

    public function pipelines(array $middlewares): Pipeline
    {
        $middlewareCallables = $this->middlewareCallables($middlewares);
        return (new Pipeline())->through($middlewareCallables);
    }
}
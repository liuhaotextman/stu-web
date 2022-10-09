<?php

namespace Snow\StuWeb\Contracts\Middleware;

use Snow\StuWeb\Middleware\Pipeline;

interface MiddlewareManagerInterface
{
    public function pipelines(array $middlewares): Pipeline;
}
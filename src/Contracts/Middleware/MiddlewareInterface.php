<?php

namespace Snow\StuWeb\Contracts\Middleware;

use Snow\StuWeb\Contracts\Http\ResponseInterface;

interface MiddlewareInterface
{
    public function handle($request, \Closure $next): ResponseInterface;
}
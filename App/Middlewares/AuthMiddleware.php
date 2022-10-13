<?php

namespace App\Middlewares;

use Closure;
use Snow\StuWeb\Contracts\Http\ResponseInterface;
use Snow\StuWeb\Contracts\Middleware\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{

    public function handle($request, Closure $next): ResponseInterface
    {
        echo 'auth middleware';
        return $next($request);
    }
}
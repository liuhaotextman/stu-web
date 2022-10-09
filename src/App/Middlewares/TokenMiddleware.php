<?php

namespace Snow\StuWeb\App\Middlewares;

use Closure;
use Snow\StuWeb\Contracts\Http\ResponseInterface;
use Snow\StuWeb\Contracts\Middleware\MiddlewareInterface;

class TokenMiddleware implements MiddlewareInterface
{

    public function handle($request, Closure $next): ResponseInterface
    {
        echo 'token middleware';
        return $next($request);
    }
}
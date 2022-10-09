<?php

namespace Snow\StuWeb\Contracts\Routing;

interface RouteItemInterface
{
    public function middleware(string $middleware): RouteItemInterface;

    public function getPattern(): string;

    public function getMethods(): array;

    public function getCallable();

    public function getMiddlewares(): array;

    /**
     * @return array|RouteProxyInterface[]
     */
    public function getGroups(): array;
}
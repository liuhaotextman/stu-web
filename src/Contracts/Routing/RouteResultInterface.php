<?php

namespace Snow\StuWeb\Contracts\Routing;

interface RouteResultInterface
{
    public function getCallable();

    public function getMiddlewares(): array;
}
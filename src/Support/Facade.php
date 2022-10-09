<?php

namespace Snow\StuWeb\Support;

use Snow\StuWeb\Contracts\Support\FacadeInterface;
use Snow\StuWeb\Http\App;

class Facade implements FacadeInterface
{
    /**
     * 始终创建新的实例
     * @var bool
     */
    protected static bool $alwaysNewInstance = false;

    protected static function createFacade(string $class = '', array $args = [], bool $newInstance = false)
    {
        $class = $class ?: static::class;
        $facadeClass = static::getFacadeClass();

        if ($facadeClass) {
            $class = $facadeClass;
        }

        if (static::$alwaysNewInstance) {
            $newInstance = true;
        }

        return App::getInstance()->make($class, $args, $newInstance);
    }

    protected static function getFacadeClass(): string
    {
        return '';
    }

    public static function __callStatic($method, $params)
    {
        return call_user_func_array([static::createFacade(), $method], $params);
    }
}
<?php

namespace Snow\StuWeb\Container;

use ArrayAccess;
use Closure;
use Exception;
use Snow\StuWeb\Contracts\Container\Container as ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;

class Container implements ArrayAccess, ContainerInterface
{
    protected static $instance;

    protected $instances = [];

    protected $bind = [];

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public static function setInstance($instance): void
    {
        static::$instance = $instance;
    }

    public function instance(string $abstract, $instance)
    {
        $abstract = $this->getAlias($abstract);
        $this->instances[$abstract] = $instance;

        return $this;
    }

    public function bind(string $abstract, $concrete)
    {
        if ($concrete instanceof Closure) {
            $this->bind[$abstract] = $concrete;
        } elseif (is_object($concrete)) {
            $this->instance($abstract, $concrete);
        } elseif(is_string($concrete)) {
            $abstract = $this->getAlias($abstract);
            if ($abstract != $concrete) {
                $this->bind[$abstract] = $concrete;
            }
        } else {
            throw new Exception('concrete type error');
        }
        return $this;
    }

    public function bindArr(array $arr)
    {
        foreach ($arr as $abstract => $concrete) {
            $this->bind($abstract, $concrete);
        }
    }

    public function has(string $abstract): bool
    {
        return isset($this->instances[$abstract]) || isset($this->bind[$abstract]);
    }

    public function get($abstract)
    {
        if ($this->has($abstract)) {
            return $this->make($abstract);
        }
        throw new Exception('class ' . $abstract . ' not found');
    }

    public function delete($name)
    {
        $name = $this->getAlias($name);

        if (isset($this->instances[$name])) {
            unset($this->instances[$name]);
        }
    }

    public function make(string $abstract, array $vars = [], bool $newInstance = false)
    {
        $abstract = $this->getAlias($abstract);
        if (isset($this->instances[$abstract]) && !$newInstance) {
            return $this->instances[$abstract];
        }

        if (isset($this->bind[$abstract]) && $this->bind[$abstract] instanceof Closure) {
            $object = $this->invokeFunction($this->bind[$abstract], $vars);
        } else {
            $object = $this->invokeClass($abstract, $vars);
        }

        if (!$newInstance) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    public function invokeFunction($function, array $vars = [])
    {
        try {
            $reflect = new ReflectionFunction($function);
        } catch (ReflectionException $exception) {
            throw new ReflectionException('函数不存在:{' . $function. '()}');
        }
        $args = $this->bindParams($reflect, $vars);
        return $function(...$args);
    }

    public function invokeMethod($method, array $vars = [], bool $accessible = false)
    {
        if (is_array($method)) {
            [$class, $method] = $method;

            $class = is_object($class) ? $class : $this->make($class);
        } else {
            [$class, $method] = explode('::', $method);
        }
        try {
            $reflect = new ReflectionMethod($class, $method);
        } catch (ReflectionException $exception) {
            $class = is_object($class) ? get_class($class): $class;
            throw new ReflectionException('method not exists: ' . $class . '::' . $method . '()');
        }

        $args = $this->bindParams($reflect, $vars);

        if ($accessible) {
            $reflect->setAccessible($accessible);
        }

        return $reflect->invokeArgs(is_object($class) ? $class : null, $args);
    }

    public function invoke($callable, array $vars = [], bool $accessible = false)
    {
        if ($callable instanceof Closure) {
            return $this->invokeFunction($callable, $vars);
        } elseif (is_string($callable) && false === strpos($callable, '::')) {
            return $this->invokeFunction($callable, $vars);
        } else {
            return $this->invokeMethod($callable, $vars, $accessible);
        }
    }

    public function invokeClass(string $class, array $vars = [])
    {
        try {
            $reflect = new ReflectionClass($class);
        } catch (ReflectionException $exception) {
            throw new ReflectionException('class 不存在:{' . $class. '()}');
        }

        $constructor = $reflect->getConstructor();
        $args = $constructor ? $this->bindParams($constructor, $vars): [];
        $object = $reflect->newInstanceArgs($args);

        return $object;
    }

    protected function bindParams(ReflectionFunctionAbstract $reflect, array $vars = []): array
    {
        if ($reflect->getNumberOfParameters() == 0) {
            return [];
        }

        reset($vars);
        $type   = key($vars) === 0 ? 1 : 0;
        $args = [];
        $parameters = $reflect->getParameters();
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $reflectionType = $parameter->getType();

            if ($reflectionType && $reflectionType->isBuiltin() === false) {
                $args[] = $this->getObjectParam($reflectionType->getName(), $vars);
            } elseif ($type == 1 && !empty($vars)) {
                $args[] = array_shift($vars);
            } elseif ($type == 0 && array_key_exists($name, $vars)) {
                $args[] = $vars[$name];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
            } else {
                throw new Exception('方法参数缺失' . $name);
            }
        }

        return $args;
    }

    protected function getObjectParam(string $className, array &$vars)
    {
        $array = $vars;
        $value = array_shift($array);
        if ($value instanceof $className) {
            array_shift($vars);
            $object = $value;
        } else {
            $object = $this->make($className);
        }

        return $object;
    }

    public function getAlias(string $abstract): string
    {
        if (isset($this->bind[$abstract])) {
            $bind = $this->bind[$abstract];
            if (is_string($bind)) {
                return $this->getAlias($bind);
            }
        }

        return $abstract;
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->bind($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }

    public function __set($name, $value)
    {
        $this->bind($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __isset($name): bool
    {
        return $this->has($name);
    }

    public function __unset($name)
    {
        $this->delete($name);
    }
}
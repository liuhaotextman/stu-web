<?php

namespace Snow\StuWeb\Middleware;

use Closure;

class Pipeline
{
    protected $passable;

    protected $pipes = [];

    protected $exceptionHandler;

    public function send($passable): self
    {
        $this->passable = $passable;
        return $this;
    }

    public function through($pipes): self
    {
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();
        return $this;
    }

    public function then(Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            function ($passable) use ($destination) {
                return $destination($passable);
            }
        );

        return $pipeline($this->passable);
    }

    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                return $pipe($passable, $stack);
            };
        };
    }

    public function whenException($handler): self
    {
        $this->exceptionHandler = $handler;
        return $this;
    }
}
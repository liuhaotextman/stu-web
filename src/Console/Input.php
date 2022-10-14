<?php

namespace Snow\StuWeb\Console;

use Snow\StuWeb\Contracts\Console\InputInterface;

class Input implements InputInterface
{
    protected string $command;

    protected string $path;

    public function __construct($argv = null)
    {
        if (null === $argv) {
            $argv = $_SERVER['argv'];
            array_shift($argv);
        }
        $this->command = $argv[0] ?? '';
        $this->path = $argv[1] ?? '';
    }

    public function command(): string
    {
        return $this->command;
    }

    public function path(): string
    {
        return $this->path;
    }

}
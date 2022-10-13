<?php

namespace Snow\StuWeb\Console;

class Input
{
    protected $tokens;

    protected $command;

    protected $path;

    public function __construct($argv = null)
    {
        if (null === $argv) {
            $argv = $_SERVER['argv'];
            array_shift($argv);
        }
        $this->tokens = $argv;
        $commands = explode(' ', $this->tokens);
        $this->command = $commands[0] ?? '';
        $this->path = $commands[1] ?? '';
    }

    public function command()
    {
        return $this->command;
    }

    public function path()
    {
        return $this->path;
    }

}
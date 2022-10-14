<?php

namespace Snow\StuWeb\Console;

use Snow\StuWeb\Contracts\Console\ConsoleInterface;
use Snow\StuWeb\Contracts\Console\InputInterface;
use Snow\StuWeb\Exception\ConsoleException;
use Snow\StuWeb\Http\App;

class Console implements ConsoleInterface
{
    protected App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function run()
    {
        $input = $this->app->make(InputInterface::class);
        $command = $input->command();
        $className = 'App\Command\\' . ucfirst($command);
        if (!class_exists($className)) {
            throw new ConsoleException('command class not found');
        }

        $commandClass = new $className();
        $commandClass->work($input);
    }
}
<?php

namespace Snow\StuWeb\Console;

use Snow\StuWeb\Http\App;

class Console
{
    protected App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function run()
    {
        $input = new Input();
        $command = $input->command();
        $appPath = $this->app->getAppPath();
        var_dump($appPath);
    }
}
<?php

namespace App\Command;

use Snow\StuWeb\Contracts\Console\CommandInterface;
use Snow\StuWeb\Contracts\Console\InputInterface;

class Queue implements CommandInterface
{
    public function work(InputInterface $input)
    {
        echo $input->path();
    }
}
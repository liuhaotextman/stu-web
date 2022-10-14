<?php

namespace Snow\StuWeb\Contracts\Console;

interface CommandInterface
{
    public function work(InputInterface $input);
}
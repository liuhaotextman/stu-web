<?php

namespace Snow\StuWeb\Contracts\Console;

interface InputInterface
{
    public function command(): string;

    public function path(): string;
}
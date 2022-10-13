<?php

namespace App\Components;

class Request extends \Snow\StuWeb\Http\Request
{
    public function get(string $name = '', string $default = null)
    {
        if ($name === '') {
            return $this->get;
        }

        return $this->get[$name] ?? $default;
    }
}
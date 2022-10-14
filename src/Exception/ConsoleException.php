<?php

namespace Snow\StuWeb\Exception;

use Throwable;

class ConsoleException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
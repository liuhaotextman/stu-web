<?php

namespace Snow\StuWeb\Support\Facades;

use Snow\StuWeb\Orm\DbManager;
use Snow\StuWeb\Support\Facade;

class Db extends Facade
{
    protected static function getFacadeClass(): string
    {
        return DbManager::class;
    }
}
<?php

namespace Snow\StuWeb\Orm\Drive;

use Snow\StuWeb\Contracts\Orm\DbDriveInterface;
use Snow\StuWeb\Orm\BaseQuery;

class MysqlPDO implements DbDriveInterface
{
    use BaseQuery;
}
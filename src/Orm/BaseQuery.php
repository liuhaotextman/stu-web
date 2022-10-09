<?php

namespace Snow\StuWeb\Orm;

use Snow\StuWeb\Exception\OrmException;

trait BaseQuery
{
    protected string $table = '';

    protected array $where = [];

    protected array $fields = [];

    protected $prefix = '';

    public function name(string $name): self
    {
        $this->table = $this->prefix . $name;
        return $this;
    }

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function where(...$where): self
    {
        if (count($where) == 1) {
            if ($where == array_values($where)) {
                array_push($this->where, $where);
            } else {
                $condition = [];
                foreach ($where as $key => $value) {
                    $condition[] = [$key, '=', $value];
                }
                array_push($this->where, $condition);
            }
        } elseif (count($where) == 2) {
            $arr = [$where[0], '=', $where[1]];
            array_push($this->where, $arr);
        } elseif (count($where) == 3) {
            $arr = [$where[0], $where[1], $where[2]];
            array_push($this->where, $arr);
        } else {
            throw new OrmException('orm query params too many');
        }
        return $this;
    }

    public function fields($fields): self
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
        }
        $this->fields = $fields;
        return $this;
    }

}
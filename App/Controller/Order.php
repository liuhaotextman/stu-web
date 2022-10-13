<?php

namespace App\Controller;

use Snow\StuWeb\Contracts\Http\RequestInterface;
use Snow\StuWeb\Support\Facades\Cache;

class Order
{
    public function index(RequestInterface $request)
    {
        Cache::set('test', 1, 2);
//        echo "<br/>";
        echo Cache::get('test');
        return ['name' => 'liuhao', 'age' => 30];
    }

    public function save()
    {
        return 'order post';
    }

    public function update()
    {
        return 'order update';
    }

    public function delete()
    {
        return 'order delete';
    }
}
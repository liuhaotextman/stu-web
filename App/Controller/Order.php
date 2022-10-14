<?php

namespace App\Controller;

use Snow\StuWeb\Contracts\Http\RequestInterface;
use Snow\StuWeb\Support\Facades\Cache;
use Snow\StuWeb\Support\Facades\Redis;

class Order
{
    public function index(RequestInterface $request)
    {
        $data = ['path' => 'order/create', 'data' => ['phone' => '15217055431']];
        Redis::lPush('queue', json_encode($data));
        return 'success';
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
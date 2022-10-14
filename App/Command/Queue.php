<?php

namespace App\Command;

use Snow\StuWeb\Contracts\Console\CommandInterface;
use Snow\StuWeb\Contracts\Console\InputInterface;
use Snow\StuWeb\Exception\ConsoleException;
use Snow\StuWeb\Support\Facades\Redis;

class Queue implements CommandInterface
{
    public function work(InputInterface $input)
    {
        //$redis = new \Redis();$redis->lPop('queue');
        while (true) {
            $message = Redis::lPop('queue');
            if ($message) {
                $message = json_decode($message, true);
                $path = $message['path'];
                $data = $message['data'];
                list($controller, $action) = explode('/', $path);
                if (!$action) $action = 'index';
                $controller = 'App\Command\Controller\\' . ucfirst($controller);
                if (!class_exists($controller)) {
                    throw new ConsoleException('command controller ' . $controller . ' not found');
                }
                call_user_func([$controller, $action], $data);
            } else {
                sleep(3);
            }
        }
    }
}
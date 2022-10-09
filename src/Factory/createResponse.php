<?php

namespace Snow\StuWeb\Factory;

use http\Exception\InvalidArgumentException;
use Snow\StuWeb\Contracts\Http\ResponseInterface;
use Snow\StuWeb\Http\Response;

class createResponse
{
    public static function createResponse($data, int $code = 200): ResponseInterface
    {
        if (is_array($data)) {
            return new Response\Json($data, $code);
        } elseif (is_string($data)) {
            return new Response\Html($data, $code);
        } elseif ($data instanceof ResponseInterface) {
            return $data;
        }
        throw new InvalidArgumentException('response参数错误');
    }
}
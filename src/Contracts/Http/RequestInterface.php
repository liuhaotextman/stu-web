<?php

namespace Snow\StuWeb\Contracts\Http;

interface RequestInterface
{
    public function header(string $name = '', string $default = null);

    public function contentType(): string;

    public function get(string $name = '', string $default = null);

    public function post(string $name = '', string $default = null);

    public function put(string $name = '', string $default = null);

    public function getMethod(): string;

    public function getPath(): string;
}
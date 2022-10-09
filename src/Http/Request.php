<?php

namespace Snow\StuWeb\Http;

use Snow\StuWeb\Contracts\Http\RequestInterface;

class Request implements RequestInterface
{
    protected string $method = '';

    protected string $path = '';

    protected $input;

    protected $param = [];

    protected $get = [];

    protected $post = [];

    protected $put;

    protected $server;

    protected $header = [];

    public function __construct()
    {

        $this->input = file_get_contents('php://input');
        $this->init();
    }

    protected function init(): void
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $arr = parse_url($_SERVER["REQUEST_URI"]);
        $this->path = $arr['path'];

        $header = [];
        $server = $_SERVER;
        foreach ($server as $key => $val) {
            if (0 === strpos($key, 'HTTP_')) {
                $key          = str_replace('_', '-', strtolower(substr($key, 5)));
                $header[$key] = $val;
            }
        }

        if (isset($server['CONTENT_TYPE'])) {
            $header['content-type'] = $server['CONTENT_TYPE'];
        }

        if (isset($server['CONTENT_LENGTH'])) {
            $header['content-length'] = $server['CONTENT_LENGTH'];
        }

        $this->header = array_change_key_case($header);
        $this->server = $server;

        $inputData = $this->getInputData($this->input);
        $this->get = $_GET;
        $this->post = $_POST ?: $inputData;
        $this->put = $inputData;
    }

    public function header(string $name = '', string $default = null)
    {
        if ($name === '') {
            return $this->header;
        }

        $name = str_replace('_', '-', strtolower($name));

        return $this->header[$name] ?? $default;
    }

    public function contentType(): string
    {
        $contentType = $this->header('Content-Type');

        if ($contentType) {
            if (strpos($contentType, ';') !== false) {
                [$type] = explode(';', $contentType);
            } else {
                $type = $contentType;
            }

            return trim($type);
        }

        return '';
    }

    public function get(string $name = '', string $default = null)
    {
        if ($name === '') {
            return $this->get;
        }

        return $this->get[$name] ?? $default;
    }

    public function post(string $name = '', string $default = null)
    {
        if ($name === '') {
            return $this->post;
        }

        return $this->post[$name] ?? $default;
    }

    public function put(string $name = '', string $default = null)
    {
        if ($name === '') {
            return $this->put;
        }

        return $this->put[$name] ?? $default;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    protected function getInputData($content): array
    {
        $contentType = $this->contentType();
        if ($contentType == 'application/x-www-form-urlencoded') {
            parse_str($content, $data);
            return $data;
        } elseif (strpos($contentType, 'json') !== false) {
            return json_decode($content, true);
        }
        return [];
    }
}
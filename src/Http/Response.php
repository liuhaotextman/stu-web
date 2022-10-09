<?php

namespace Snow\StuWeb\Http;

use Snow\StuWeb\Contracts\Http\ResponseInterface;

class Response implements ResponseInterface
{
    protected array $options = [];

    protected array $data = [];

    protected $allowCache = false;

    protected array $header = [];

    /**
     * @var mixed|string|null
     */
    protected $content = null;

    protected int $code = 200;

    protected string $contentType = 'text/html';

    protected string $charset = 'utf-8';

    public function __construct($content = '', int $code = 0)
    {
        $this->content = $content;
        $this->code = $code;

        $this->contentType($this->contentType, $this->charset);
    }

    public function send(): void
    {
        $data = $this->getContent();

        if (!headers_sent()) {
            if (!empty($this->header)) {
                http_response_code($this->code);
                foreach ($this->header as $name => $val) {
                    header($name . (!is_null($val) ? ':' . $val : ''));
                }
            }
        }

        $this->sendData($data);
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    public function options(array $options = []): ResponseInterface
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function data($data): ResponseInterface
    {
       $this->data = $data;

       return $this;
    }

    public function allowCache(bool $cache): ResponseInterface
    {
        $this->allowCache = $cache;

        return $this;
    }

    public function isAllowCache(): bool
    {
        return $this->allowCache;
    }

    public function header(array $header = []): ResponseInterface
    {
        $this->header = array_merge($this->header, $header);

        return $this;
    }

    public function content(string $content): ResponseInterface
    {
        $this->content = $content;

        return $this;
    }

    public function code(int $code): ResponseInterface
    {
        $this->code = $code;

        return $this;
    }

    public function lastModified(string $time): ResponseInterface
    {
        $this->header['Last-Modified'] = $time;

        return $this;
    }

    public function expires(string $time): ResponseInterface
    {
        $this->header['Expires'] = $time;

        return $this;
    }

    public function eTag(string $eTag): ResponseInterface
    {
        $this->header['eTag'] = $eTag;

        return $this;
    }

    public function cacheControl(string $cache): ResponseInterface
    {
        $this->header['Cache-control'] = $cache;

        return $this;
    }

    public function contentType(string $contentType, string $charset = 'utf-8'): ResponseInterface
    {
        $this->header['Content-Type'] = $contentType . ';charset=' . $charset;

        return $this;
    }

    public function getHeader(string $name = '')
    {
        if (!$name) {
            return $this->header;
        }

        return $this->header[$name] ?? null;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    protected function sendData(string $data): void
    {
        echo $data;
    }
}
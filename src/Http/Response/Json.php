<?php

namespace Snow\StuWeb\Http\Response;

use Snow\StuWeb\Http\Response;

class Json extends Response
{
    protected array $options = [
        'json_encode_param' => JSON_UNESCAPED_UNICODE
    ];

    protected string $contentType = 'application/json';

    public function __construct($data, int $code = 200)
    {
        $this->data = $data;
        $this->code = $code;
        $this->contentType($this->contentType, $this->charset);
    }

    public function getContent(): string
    {
        try {
            $data = json_encode($this->data, $this->options['json_encode_param']);

            if (false === $data) {
                throw new \InvalidArgumentException(json_last_error_msg());
            }

            return $data;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
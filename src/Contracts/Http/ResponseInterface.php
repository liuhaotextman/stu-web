<?php

namespace Snow\StuWeb\Contracts\Http;

interface ResponseInterface
{
    public function send(): void;

    public function options(array $options = []): self;

    public function data($data): self;

    public function allowCache(bool $cache): self;

    public function isAllowCache(): bool;

    public function header(array $header = []): self;

    public function content(string $content): self;

    public function code(int $code): self;

    public function lastModified(string $time): self;

    public function expires(string $time): self;

    public function eTag(string $eTag): self;

    public function cacheControl(string $cache): self;

    public function contentType(string $contentType, string $charset = 'utf-8'): self;

    public function getHeader(string $name = '');

    public function getData();

    public function getContent(): string;

    public function getCode(): int;
}
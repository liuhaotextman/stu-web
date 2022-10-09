<?php

namespace Snow\StuWeb\Cache\Driver;

use Snow\StuWeb\Contracts\Cache\CacheDriverInterface;
use Snow\StuWeb\Filesystem\Filesystem;

class FileCacheDriver implements CacheDriverInterface
{

    protected Filesystem $filesystem;

    protected string $cachePath = '/var/www/html/stu_web/runtime/';

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    protected function getKeyFileName(string $key)
    {
        return $this->cachePath . $key . '.log';
    }

    public function get(string $key)
    {
        $fileName = $this->getKeyFileName($key);
        $content = $this->filesystem->read($fileName);
        if ($content === false) {
            return false;
        }
        $content = unserialize($content);
        if (isset($content['expire_time']) && $content['expire_time'] < time()) {
            $this->filesystem->delete($fileName);
            return false;
        }
        return $content['content'];
    }

    public function set(string $key, $value, int $expire = 0)
    {
        $fileName = $this->getKeyFileName($key);
        $inputContent = [];
        if ($expire) {
            $inputContent['expire_time'] = time() + $expire;
        }
        $inputContent['content'] = $value;
        $inputContent = serialize($inputContent);
        return $this->filesystem->write($fileName, $inputContent);
    }
}
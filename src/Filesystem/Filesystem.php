<?php

namespace Snow\StuWeb\Filesystem;

class Filesystem
{
    public function write(string $fileName, $content)
    {
        return file_put_contents($fileName, $content);
    }

    public function read(string $fileName)
    {
        return file_get_contents($fileName);
    }

    public function delete(string $fileName)
    {
        if (!file_exists($fileName)) {
            return false;
        }

        return unlink($fileName);
    }
}
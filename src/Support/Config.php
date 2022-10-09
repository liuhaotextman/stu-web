<?php

namespace Snow\StuWeb\Support;

use Snow\StuWeb\Contracts\Support\ConfigInterface;
use Snow\StuWeb\Exception\FileException;
use Snow\StuWeb\Http\App;

class Config implements ConfigInterface
{
    protected App $app;

    protected array $data = [];

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function load(string $configFile): array
    {
        if (!file_exists($configFile)) {
            throw new FileException('file ' . $configFile . ' not exists');
        }
        $type = pathinfo($configFile, PATHINFO_EXTENSION);
        if (!in_array(['php', 'json', 'yml', 'yaml', 'ini'], $type)) {
            throw new FileException('wrong file extension, can not parse' . $configFile);
        }
        $config = [];
        switch ($type) {
            case 'php':
                $config = require $configFile;
                break;
            case 'json':
                $config = json_decode(file_get_contents($configFile), true);
                break;
            case 'yml':
            case 'yaml':
                $config = yaml_parse_file($configFile);
                break;
            case 'ini':
                $config = parse_ini_file($configFile);
                break;
        }

        $this->data = $config;
        return $config;
    }

    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->data;
        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return $default;
            }
        }

        return $value;
    }
}

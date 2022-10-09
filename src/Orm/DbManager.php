<?php

namespace Snow\StuWeb\Orm;

use Snow\StuWeb\Contracts\Orm\DbDriveInterface;
use Snow\StuWeb\Contracts\Orm\DbManagerInterface;
use Snow\StuWeb\Exception\FileException;
use Snow\StuWeb\Exception\OrmException;
use Snow\StuWeb\Http\App;
use Snow\StuWeb\Orm\Drive\Mysql;
use Snow\StuWeb\Orm\Drive\Mysqli;
use Snow\StuWeb\Orm\Drive\MysqlPDO;

class DbManager implements DbManagerInterface
{
    protected App $app;

    protected array $config = [];

    protected string $type = 'pdo';

    protected array $driveMaps = [
        'pdo' => MysqlPDO::class,
        'mysql' => Mysql::class,
        'mysqli' => Mysqli::class
    ];

    /**
     * @var DbDriveInterface
     */
    protected $drive = null;

    public function __construct(App $app)
    {
        $this->app = $app;
        $configFile = $this->app->getConfigPath() . DIRECTORY_SEPARATOR . 'database.php';
        if (!file_exists($configFile)) throw new FileException('config file ' . $configFile . ' not exists');
        $config = require $configFile;
        $this->config = [
            'hostname' => $config['hostname'],
            'port' => $config['port'],
            'username' => $config['username'],
            'password' => $config['password'],
            'database' => $config['database'],
            'charset' => $config['charset'] ?? 'utf8mb4',
            'prefix' => $config['prefix'] ?? ''
        ];

        isset($config['type']) && $config['type'] && $this->type = $config['type'];
    }

    public function getDrive(): DbDriveInterface
    {
        if (!$this->drive) {
            if (!array_key_exists($this->type, array_keys($this->driveMaps))) {
                throw new OrmException('database config error');
            }

            $driveName = $this->driveMaps[$this->type];
            $drive = $this->app->make($driveName);
            $this->drive = $drive;
        }

        return $this->drive;
    }
}
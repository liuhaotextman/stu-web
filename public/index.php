<?php

use Snow\StuWeb\Http\App;

ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

require __DIR__ . '/../vendor/autoload.php';
$app = App::getInstance();
$response = $app->run();
$response->send();


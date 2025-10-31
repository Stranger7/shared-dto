<?php

use DTOCompiler\Compiler;

if (!file_exists(dirname(__DIR__) . '/.env')) {
    echo "file .env not found\n";
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

define('YII_DEBUG', env('APP_DEBUG', 'no') !== 'no');
define('YII_ENV', env('APP_ENV', 'production'));

require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

(new Compiler($argv[1] ?? ''))->run();

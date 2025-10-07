<?php

use DTOCompiler\Compiler;

if (!file_exists(dirname(__DIR__) . '/.env')) {
    echo "file .env not found\n";
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

(new Compiler($argv[1] ?? ''))->run();

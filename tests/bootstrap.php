<?php

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('Unika\Tests', __DIR__);

//define the root directory
\Unika\Application::$ROOT_DIR = realpath('../');
\Unika\Application::$ENVIRONMENT = 'testing';
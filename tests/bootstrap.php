<?php
gc_disable();

require __DIR__.'/../bootstrap.php';

\Unika\Application::$ENVIRONMENT = 'testing';

define('TEST_ROOT',__DIR__);
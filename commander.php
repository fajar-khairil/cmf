<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */
 
$app = require_once 'bootstrap.php';

// Register default Command here
$app['console']->add(new \Unika\Command\CacheCommand());
$app['console']->add(new \Unika\Command\ViewCommand());

$app['console']->run();
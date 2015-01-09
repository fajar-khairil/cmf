<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */
 
$app = require_once 'bootstrap.php';

$Commander = new \Symfony\Component\Console\Application('UnikaCommander','0.1-dev');

// Register default Command here
$Commander->add(new \Unika\Command\CacheCommand());

$Commander->run();
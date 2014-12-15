<?php
/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */
 
$app = require_once 'bootstrap.php';

$Commander = new \Symfony\Component\Console\Application('Unika-Commander','0.1-dev');

$Commander->run();
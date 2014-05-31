<?php
/**
 *
 *  Bootstraping the Web App
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

$configs = require_once 'bootstrap.php';

$app = new \Unika\Application($configs);

require 'routes.php';

$app->run();
<?php
/**
 *
 *  Bootstraping the Web App
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

define('ENGINE_PATH', __DIR__);
define('APC_PRESENT',extension_loaded('apc') AND (boolean)ini_get('apc.enabled'));

require 'vendor/autoload.php';

$configs = require 'config/app.php';

$app = new Unika\Application($configs);

require 'routes.php';

$app->run();
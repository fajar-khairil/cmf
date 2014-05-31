<?php
/**
 *
 *  Bootstraping the App
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

define('APC_PRESENT',extension_loaded('apc') AND (boolean)ini_get('apc.enabled'));

require 'vendor/autoload.php';

\Unika\Application::$BACKEND_URI = 'administrator';
\Unika\Application::$ENGINE_PATH = __DIR__;

$configs = require 'config/app.php';

$configs['environtments'] = array
(
	'local'	=> array(
		'manjaro','fajardev'
	),
	'staging'	=>	array(
		'your-machine'
	)
);

return $configs;
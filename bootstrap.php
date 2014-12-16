<?php
/**
 *	Custom execution script
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

require_once 'vendor/autoload.php';

error_reporting(-1);

use Unika\Application as Application;

//define the root directory
Application::$ROOT_DIR = __DIR__;

$environtments = array(
    'local' => array(
        'fajardev'
    ),
    'staging'   =>  array(
        'staging-machine'
    )
);

foreach( $environtments as $env=>$machine )
{
	if( in_array(gethostname(),$machine) )
	{
		Application::$ENVIRONMENT = $env;
		break;
	}
}

$app = new Application();

/**
 * Registering some ServiceProvider you can disabled if you dont need it
 */
$app->register(new \Unika\Provider\IlluminateServiceProvider());

$app->register(new \Unika\Provider\MonologServiceProvider(),
    array(
        'monolog.logfile'       =>  $app::$ROOT_DIR.'/var/logs/application.log',
        'monolog.permission'    =>  0777
    )
);

$app->register(new \Unika\Provider\ViewServiceProvider());

$app->register(new Unika\Provider\CacheServiceProvider());
//$app->register(new Unika\Provider\DatabaseServiceProvider());
$app->register(new Unika\Provider\AclServiceProvider());

return $app;
<?php
/**
 *	Custom execution script
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

require_once 'vendor/autoload.php';
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

$app = new Application();

//detect environment
$app->detectEnvironment(function() use($environtments){
    
    $env = 'production';//default
    foreach( $environtments as $env=>$machine )
    {
        if( in_array(gethostname(),$machine) )
        {
            $result = $env;
            break;
        }    
    }
    return $env;
});

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

$app->register(new Unika\Provider\CacheServiceProvider());
$app->register(new Unika\Provider\DatabaseServiceProvider());
$app->register(new \Unika\Provider\ViewServiceProvider());
$app->register(new Unika\Provider\AclServiceProvider());

return $app;
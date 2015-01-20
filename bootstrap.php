<?php
/**
 *  This file is part of the UnikaCMF project
 *
 *	Custom execution script
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

/** Uncomment this function when you are ready to production*/
//function dd(){return;};
$loader = require_once 'vendor/autoload.php';

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

//detect environment
Application::detectEnvironment(function() use($environtments){
    
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

$app = new Application();
$app->setLoader($loader);

/**
 * Registering Core ServiceProvider you can disabled if you dont need it
 */
$app->register(new \Silex\Provider\LocaleServiceProvider(),array(
    'locale_fallbacks'  => 'en'
));
$app->register(new \Silex\Provider\TranslationServiceProvider());
$app->register(new \Unika\Provider\MonologServiceProvider(),
    array(
        'monolog.logfile'       =>  $app['path.var'].'/logs/'.$app->config('app.name').'.log',
        'monolog.permission'    =>  0777
    )
);
$app->register(new \Unika\Provider\SessionServiceProvider());
$app->register(new \Unika\Provider\CacheServiceProvider());
$app->register(new \Unika\Provider\DatabaseServiceProvider());
$app->register(new \Unika\Provider\ViewServiceProvider());
$app->register(new \Unika\Provider\AuthServiceProvider());
$app->register(new \Unika\Provider\AclServiceProvider());

return $app;
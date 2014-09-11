<?php
/**
 *
 *  Bootstraping the App
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

define('APC_PRESENT',extension_loaded('apc') AND (boolean)ini_get('apc.enabled'));

date_default_timezone_set('Asia/Jakarta');

require 'vendor/autoload.php';

Application::$ENGINE_PATH = __DIR__;
Application::$BACKEND_URI = 'administrator';

//environtment detection by machine name
$environtments = array(
    'local' => array(
        'fajardev'
    ),
    'staging'   =>  array(
        'staging-machine'
    )
);

Application::detectEnvirontment( $environtments );

//construct the Application

$app = new \Application();

//Resgistering Services

$app->register(new \Silex\Provider\HttpCacheServiceProvider(),array(
    'http_cache.cache_dir'  => $app['config']['app.tmp_dir'].DIRECTORY_SEPARATOR.'cache'
));

$app->register(new \Silex\Provider\MonologServiceProvider(),array(
    'monolog.logfile'   => $app['config']->get('app.log_dir').DIRECTORY_SEPARATOR.'application.log'
));     

$app->register(new \Unika\Provider\SessionServiceProvider());
$app->register(new \Unika\Provider\SecurityServiceProvider);
$app->register(new \Unika\Provider\CacheServiceProvider);
$app->register(new \Unika\Provider\CapsuleServiceProvider);      
$app->register(new \Unika\Provider\ViewServiceProvider);
$app->register(new \Unika\Provider\AclServiceProvider);

$app->register(new \Silex\Provider\TranslationServiceProvider);       
$app->register(new \Silex\Provider\SwiftmailerServiceProvider);
$app->register(new \Silex\Provider\ServiceControllerServiceProvider);        
$app->register(new \Silex\Provider\RoutingServiceProvider);  

//include routes
require_once '../routes.php';

//cache control
$app->after(function($request,$response){
	$response->headers->addCacheControlDirective('no-cache', true);
	$response->headers->addCacheControlDirective('max-age', 0);
	$response->headers->addCacheControlDirective('must-revalidate', true);
	$response->headers->addCacheControlDirective('no-store', true);
});

//reserved for end developer
/*$app->error(function($e,$request){
	
});*/
       
//run the app 
$app->run();
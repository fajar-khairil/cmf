<?php
/**
 *
 *  Bootstraping the Web App
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

require_once 'bootstrap.php';

$app = new \Application();

require '../routes.php';

$app->after(function($request,$response){
	$response->headers->addCacheControlDirective('no-cache', true);
	$response->headers->addCacheControlDirective('max-age', 0);
	$response->headers->addCacheControlDirective('must-revalidate', true);
	$response->headers->addCacheControlDirective('no-store', true);
});

//reserved for end developer
/*$app->error(function($e,$request){
	
});*/

$app->run();
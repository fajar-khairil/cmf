<?php

//mounting Backend URI
$app->match('/'.$app['config']['app.backend_uri'].'/{actionName}',function($actionName) use($app){
	$controller = new Controller_AuthController($app);
	$method = "{$actionName}Action";
	if (!method_exists($controller,$method)) {
		$app->abort(404);
	}

	return $controller->$method($app['request']);	
})
->method('GET|POST')
->value('actionName','index');


//generic
$app->match('/{controllerName}/{actionName}', function ($controllerName, $actionName) use ($app) 
{
	$controllerName = ucfirst($controllerName);
	 
	$class = "Controller_{$controllerName}Controller";
	$method = "{$actionName}Action";
	 
	if (!class_exists($class)) {
		$app->abort(404);
	}

	$reflection = new ReflectionClass($class);
	if (!$reflection->hasMethod($method)) {
		$app->abort(404);
	}
	 
	$controller = new $class($app);
	return $controller->$method($app['request']);	
})
->value('controllerName', 'index')
->value('actionName', 'index');
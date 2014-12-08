<?php
/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

//global route
$app->get('/{args}', function ($args) use($app) {	
	//get controller and action
	if( empty($args[0]) )
		$classController = 'Controller_IndexController';
	else
		$classController = 'Controller_'.ucfirst($args[0]).'Controller';
	
	$action = (isset($args[1])) ? strtolower($args[1]).'Action': 'indexAction';

	//checking classController
	if( !class_exists($classController) ){
		$app->abort(404);
	}

	//build classController and execute
	$controller = new $classController($app);

	//check existence of methodAction
	if( !method_exists($classController, $action) ){
		$app->abort(404);
	}	

	//all is ok, create main request
    return $controller->{$action}(\Symfony\Component\HttpFoundation\Request::createFromGlobals());
})
->assert('args', '.*')
->convert('args', function ($args) {
    return explode('/', $args);
})
->bind('default');
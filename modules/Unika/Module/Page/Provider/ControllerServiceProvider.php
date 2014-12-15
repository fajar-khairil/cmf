<?php
/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Module\Page\Provider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class ControllerServiceProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/{args}', function (Application $app) {
			//get controller and action
			if( empty($args[0]) )
				$classController = '\Unika\Module\Page\Controller\IndexController';
			else
				$classController = '\Unika\Module\Page\Controller\\'.ucfirst($args[0]).'Controller';
			
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
		    return $controller->{$action}( \Symfony\Component\HttpFoundation\Request::createFromGlobals() );
        })
        ->assert('args', '.*')
		->convert('args', function ($args) {
		    return explode('/', $args);
		})
		->bind('default');

        return $controllers;
    }	
}
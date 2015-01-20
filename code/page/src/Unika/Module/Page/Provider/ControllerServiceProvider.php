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

        $controllers->match('/{args}', function (Application $app,$args) {
			//build classController
			$controller = new $args['controller']($app);
			$action = $args['action'];

			//check existence of methodAction
			if( !method_exists($controller, $action) ){
				$app->abort(404);
			}	

			//all is ok, execute the controller
		    return $controller->{$action}( \Symfony\Component\HttpFoundation\Request::createFromGlobals() );
        })
        ->assert('args', '.*')
		->convert('args', function ($args,$app) {
		    $argv = explode('/', $args);

        	$ctrlName = ( empty($argv[0]) ) ? 'Index' : ucfirst($argv[0]);

			$classController = '\Unika\Module\Page\Controller\\'.$ctrlName.'Controller';
			
			$actionName = ( empty($argv[1]) ) ? 'index' : strtolower($argv[1]);

			$action = $actionName.'Action';

			//checking classController
			if( !class_exists($classController) ){
				$app->abort(404);
			}

			return array(
				'controller'	=> $classController,
				'action'		=> $action 
			);
		})
		->bind('page')
		->method('GET|POST|PUT')
		->compile();

        return $controllers;
    }	
}
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

	    $controllers->match('/{controller}/{action}/{_locale}', function (Application $app,$controller,$action,$_locale) 
        {
			$classController = '\Unika\Module\Page\Controller\\'.ucfirst($controller).'Controller';

			//checking classController
			if( !class_exists($classController) ){
				$app->abort(404);
			}

			//build classController
			$controller = new $classController($app);
			$action = strtolower($action).'Action';

			//check existence of methodAction
			if( !method_exists($controller, $action) ){
				$app->abort(404);
			}	

			//all is ok, execute the controller
		    return $controller->{$action}( \Symfony\Component\HttpFoundation\Request::createFromGlobals() );
        })
	    ->value('controller','Index')
	    ->value('action','index')
	    ->assert('_locale', implode('|',$app->config('app.locales')) )
        ->value('_locale',$app->config('app.default_locale'))    
		->bind('page')
		->method('GET|POST|PUT')
		->compile();

        return $controllers;
    }	
}
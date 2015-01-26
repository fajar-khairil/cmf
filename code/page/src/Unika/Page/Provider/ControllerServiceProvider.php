<?php
/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Page\Provider;

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
			$classController = '\Unika\Page\Controller\\'.ucfirst($controller).'Controller';

			//checking classController
			if( !class_exists($classController) ){
				$app->abort(404);
			}

			//build classController
			$controller = new $classController($app);

			//all is ok, execute the controller
		    return $controller->execute( \Symfony\Component\HttpFoundation\Request::createFromGlobals() , strtolower($action).'Action' );
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
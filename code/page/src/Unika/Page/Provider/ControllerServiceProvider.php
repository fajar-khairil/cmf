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

        $controllers
        ->value('_locale',$app->config('app.default_locale'))
        ->convert('_locale',function($_locale) use($app){
        	if( !in_array($_locale,$app->config('app.locales')) )
        	{
        		return 'id';
        	}

        	return $_locale;
        })
        ->before(function($request,$app){
        	$path = explode('/',$request->getPathInfo());

        	if( !in_array($path[1],$app->config('app.locales')) )
        	{
        		if( '/' === $request->getPathInfo() )
        			$newUri = $request->getBasePath().'/id';
        		else
        			$newUri = $request->getBasePath().'/id'.$request->getPathInfo();

        		return $app->redirect($newUri);
        	}
        });

	    $controllers->match('/{_locale}/{controller}/{action}', function (Application $app,$_locale,$controller,$action) 
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
		->method('GET|POST|PUT')
		->bind('page')
		->compile();

        return $controllers;
    }	
}
<?php
/**
 *	This file is part of the Unika-CMF project.
 *  Filters Class for Reusable Filter function
 *
 *  @license MIT
 *  @author Fajar Khairil
 */

use Symfony\Component\HttpFoundation\Request;

class Filters 
{
	//backend filter	
	public static function backendFilter($request,\Silex\Application $app)
	{
		if( !$app['auth']->check() )
		{
			$app['session']->getFlashBag()->add('notice','you must be logged in');
			return $app->redirect($app['config']['app.backend_uri'].'/login');
		}
	}
}
<?php
/**
 *	
 *	BackendControllerProvider
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika;

use Silex\ControllerProviderInterface;
use Unika\Application;
use Symfony\Component\HttpFoundation\Request;

class BackendControllerProvider implements ControllerProviderInterface
{
	public function connect(\Silex\Application $app)
	{
		// creates a new controller based on the default route
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function (Application $app) {
			return $app['twig']->render('default/layout.twig',array('page_title' => "Dashboard"));
		})->bind('backend');

		$controllers->get('/login', function (Application $app) {
			return $app['twig']->render('default/login.twig',array('page_title' => "Signin"));
		});

		$controllers->get('/logout', function (Application $app) {
			return 'logout!';
		});

		$controllers->post('/login_check', function (Application $app) {
			$request = $app['request'];
			return $request->request->get('_username');
		});

		return $controllers;	
	}


}
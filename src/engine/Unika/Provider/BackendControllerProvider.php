<?php
/**
 *	
 *	BackendControllerProvider
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Provider;

use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class BackendControllerProvider implements ControllerProviderInterface
{
	public function connect(\Silex\Application $app)
	{
		// creates a new controller based on the default route
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function (\Application $app) 
		{
			$auth = new \Unika\Security\Eloquent\Auth($app);
			if( !$auth->check() )
			{
				return $app->redirect('/administrator/login');
			}

			return $app['twig']->render('default/layout.twig',array('page_title' => "Dashboard"));
		})->bind('backend');

		$controllers->get('/login', function (\Application $app) {
			$auth = new \Unika\Security\Eloquent\Auth($app);
			if( $auth->check() )
			{
				return $app->redirect('/administrator');
			}
			return $app['twig']->render('default/login.twig',array('page_title' => "Signin"));
		});

		$controllers->get('/logout', function (\Application $app) {
			$auth = new \Unika\Security\Eloquent\Auth($app);
			$auth->logout();

			$app['session']->getFlashBag()->add('notice','logged_out successfully');

			return $app->redirect('/administrator/login');
		});

		$controllers->post('/login_check', function (\Application $app) {
			$post = $app['request']->request->all();

			$auth = new \Unika\Security\Eloquent\Auth($app);
			$result = $auth->attempt(['username' => $post['_username'],'pass' => $post['_password'] ],(bool)array_get($post,'_remember'));
			
			if( $result )
			{
				$app['session']->getFlashBag()->add('notice','successfully logged in');
				return $app->redirect('/administrator');
			}
			else
			{
				$app['session']->getFlashBag()->add('notice','Wrong Credentials');
				//dd($app['session']->getFlashBag()->get('notice'));
				return $app->redirect('/administrator/login');
			}
		});

		return $controllers;	
	}


}
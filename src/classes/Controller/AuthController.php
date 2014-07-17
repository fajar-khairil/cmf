<?php
/**
 *	This file is part of the Unika-CMF project.
 *  Authentication Controller
 *
 *  @license MIT
 *  @author Fajar Khairil
 */

use Symfony\Component\HttpFoundation\Request;

class Controller_AuthController extends Controller_BaseController
{
	public function indexAction(Request $request)
	{
		//dd(count( $this->app['session']->getFlashBag()->get('notice') ));
		return $this->app['view']->make('backend::default.dashboard')->with('page_title',"Dashboard");
	}

	public function loginAction(Request $request)
	{
		$auth = $auth = $this->app['auth'];
		if( $auth->check() )
		{
			return $this->app->redirect('/administrator');
		}

		return $this->app['view']->make('backend::default.login',['page_title' => 'Signin']);
	}

	public function logoutAction(Request $request)
	{
		$auth = $auth = $this->app['auth'];
		if( $auth->check() )
		{				
			$auth->logout();
			$this->app['session']->getFlashBag()->add('notice','logged out successfully');
		}

		return $this->app->redirect('/administrator/login');
	}

	//post only
	public function login_checkAction(Request $request)
	{
		if( $request->getMethod() !== 'POST' ){
			$this->app->abort(404,'Page not found.');
		}

		$post = $this->app['request']->request->all();

		$auth = $auth = $this->app['auth'];
		$result = $auth->attempt(['username' => $post['_username'],'pass' => $post['_password'] ],(bool)array_get($post,'_remember'));
		
		if( $result )
		{
			$this->app['session']->getFlashBag()->add('notice','successfully logged in');
			return $this->app->redirect('/administrator');
		}
		else
		{
			$this->app['session']->getFlashBag()->add('notice','Wrong Credentials');
			return $this->app->redirect('/administrator/login');
		}
	}
}
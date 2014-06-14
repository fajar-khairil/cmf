<?php

use Symfony\Component\HttpFoundation\Request;

class Controller_IndexController extends Controller_BaseController
{
	public function indexAction(Request $request)
	{
		return $this->app['view']->render('default/test')->with('page_title','Welcome to the jungle!');
		if( $this->app['auth']->check() )
			return 'Hello World!<br>You are logged in to application.';
		else
			return 'Hello World<br>you are not logged in.';
	}
}
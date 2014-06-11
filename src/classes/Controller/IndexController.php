<?php

use Symfony\Component\HttpFoundation\Request;

class Controller_IndexController extends Controller_BaseController
{
	public function indexAction(Request $request)
	{
		$auth = $this->app['auth'];
		if( $auth->check() )
		{
			return 'Hello World!';
		}
		else
		{
			return 'You dont have permission.';
		}
	}
}
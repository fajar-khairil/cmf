<?php

/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

use Symfony\Component\HttpFoundation\Request;

class Controller_IndexController extends Controller
{
	public function indexAction(Request $request)
	{
		return $this->app->createResponse($this->app['view']->make('home')->with('page_title','<strong>Welcome</strong>')->render());
	}

	public function errorAction(Request $request)
	{
		throw new RuntimeException('Maho');
	}
}
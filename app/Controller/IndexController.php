<?php

/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

use Symfony\Component\HttpFoundation\Request;

class Controller_IndexController extends BaseController
{
	public function indexAction(Request $request)
	{
		dd($this->app['cache']);
		$response = $this->app->createResponse($this->app['view']->make('home')->with('page_title','<strong>Welcome</strong>')->render());		
		$this->app['logger']->addInfo('awesome!');
		return $response;
	}

	public function errorAction(Request $request)
	{
		throw new RuntimeException('Maho');
	}
}
<?php

/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Module\Page\Controller;

use Symfony\Component\HttpFoundation\Request;

class IndexController extends \Unika\Controller
{ 
	public function indexAction(Request $request)
	{
		$response = $this->app->createResponse($this->app['view']->make('home')->render());
		return $response;
	}

	public function testAction(Request $request)
	{
		return $this->app->trans('hello',array('%name%' => 'Fajar'));
	}
}
<?php

/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Page\Controller;

use Symfony\Component\HttpFoundation\Request;

class IndexController extends \Unika\Controller
{ 
	public function indexAction()
	{
		$response = $this->app->createResponse($this->app['view']->make('home')->render());
		return $response;
	}

	public function testAction()
	{
		return $this->app->trans('Hello',array('%name%' => 'Fajar'));
	}
}
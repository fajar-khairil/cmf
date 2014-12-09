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
		$acl = $this->app['acl'];

		//$acl->allow(1,'post',['review','create']);
		$acl->deny(1,'post',['review']);

		$acl_dump = \Unika\Util::var_dump($acl->isAllowed('post','read',1));
		$response = $this->app->createResponse($this->app['view']->make('home')->with('page_title','<strong>Welcome</strong>')->render());
		return $response.$acl_dump;
	}

	public function errorAction(Request $request)
	{
		throw new RuntimeException('Maho');
	}
}
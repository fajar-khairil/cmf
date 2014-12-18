<?php

/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */
namespace Unika\Module\Page\Controller;

use Symfony\Component\HttpFoundation\Request;

class IndexController extends \Unika\BaseController
{
	public function indexAction(Request $request)
	{
		$acl = $this->app['acl'];
		$acl->isAllowed('Post','read','Editor');

		$response = $this->app->createResponse($this->app['view']->make('home')->with('page_title','<strong>Welcome</strong>')->render());
		return $response;
	}

	public function errorAction(Request $request)
	{
		throw new \RuntimeException('Maho');
	}
}
<?php

use Symfony\Component\HttpFoundation\Request;

class Controller_IndexController extends Controller_BaseController
{
	public function indexAction(Request $request)
	{
		//dd($this->app->path('/administrator/login'));
		return 'Hello World!<br>Work In Progress';
	}
}
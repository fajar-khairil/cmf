<?php
/**
 *  This file is part of the Unika-CMF project.
 *  Home Controller
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

use Symfony\Component\HttpFoundation\Request;

class Controller_HomeController extends Controller_BaseController
{
	public function indexAction(Request $request)
	{
		if( $this->app['auth']->check() )
			return $this->app['view']->make('backend::default.test',['page_title' => 'Your App Configurations : ']);
		else
			return 'please <a href="'.$this->app->path('backend',['actionName' => 'login']).'">logged in</a>';
	}
}
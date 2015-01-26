<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika;

use Symfony\Component\HttpFoundation\Request;

Abstract class Controller
{
	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
		$this->init();
	}

	public function execute(Request $request,$actionName)
	{
		if( !method_exists($this, $actionName) )
		{
			$this->app->abort(404);
		}

		$this->before($request);

		return $this->after($request,$this->{$actionName}($request));
	}
	
	// to be overide by concreate class
	protected function init()
	{}

	// to be overide by concreate class
	protected function before(Request $request)
	{}

	// to be overide by concreate class
	protected function after(Request $request,$response)
	{
		return $response;
	}
}
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
	protected $request;
	protected $actionName;
	protected $method;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function execute(Request $request,$actionName)
	{
		if( !method_exists($this, $actionName) )
		{
			$this->app->abort(404);
		}

		$this->actionName = $actionName;
		$this->method = $request->getMethod();

		$this->request = $request;
		$this->before();

		return $this->after($this->{$actionName}());
	}
	
	protected function post($field = null,$default = null)
	{
		if( null === $field )
			return $this->request->request;

		return $this->request->request->get($field,$default);
	}

	protected function query($field = null,$default = null)
	{
		if( null === $field )
			return $this->request->query;

		return $this->request->query->get($field,$default);
	}

	// to be overide by concreate class
	protected function before()
	{}

	// to be overide by concreate class
	protected function after($response)
	{
		return $response;
	}
}
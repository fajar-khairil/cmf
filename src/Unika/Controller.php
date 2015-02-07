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

	protected $styles = array();
	protected $head_scripts = array();
	protected $scripts = array();

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

	protected function view($view,array $data = null)
	{
		if( null === $data )
			$data = array();
		
		$data['head_scripts'] = $this->head_scripts;
		$data['scripts'] = $this->scripts;
		$data['styles'] = $this->styles;

		return $this->app['view']->make($view)->with($data);
	}

	protected function styles($styles)
	{
		if( is_array($styles) ){
			foreach ($styles as $style) {
				$this->styles[] = $style;
			}
		}
		else{
			$this->styles[] = $styles;
		}
	}

	protected function scripts($scripts)
	{
		if( is_array($scripts) ){
			foreach ($scripts as $script) {
				$this->scripts[] = $script;
			}
		}
		else{
			$this->scripts[] = $scripts;
		}
	}

	protected function headScripts($head_scripts)
	{
		if( is_array($head_scripts) ){
			foreach ($head_scripts as $script) {
				$this->head_scripts[] = $script;
			}
		}
		else{
			$this->head_scripts[] = $head_scripts;
		}
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
		if( $response instanceof \Symfony\Component\HttpFoundation\Response )
			return $response;

		if( is_object($response) AND method_exists($response, 'render') )
			return $this->app->createResponse( $response->render() );
		else
			return $this->app->createResponse($response);
	}
}
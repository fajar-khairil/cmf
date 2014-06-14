<?php
/**
 *	This file is part of the Unika-CMF project.
 *	SessionWrapper
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Common;

class ViewWrapper
{
	protected $app;

	public function __construct(\Pimple\Container $app)
	{
		$this->app = $app;
	}

	public function render($viewPath,$data = array())
	{
		$viewPath = $this->app['view.finder']->find($viewPath);

        $engine = $this->app['view.factory']->getEngineFromPath($viewPath);
        
        $view = new \Illuminate\View\View($this->app['view.factory'],$engine,'',$viewPath,$data);
		
        return $view;
	}
}
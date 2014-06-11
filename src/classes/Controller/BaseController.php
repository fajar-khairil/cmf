<?php
/**
 *	
 *	Base Controller for Backend
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

abstract class Controller_BaseController
{
	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}
}
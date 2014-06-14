<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Base Controller
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
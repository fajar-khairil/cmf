<?php

/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

use Symfony\Component\HttpFoundation\Request;
use Unika\Application;

Abstract class Controller
{
	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}
}
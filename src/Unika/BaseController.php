<?php
/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika;

Abstract class BaseController
{
	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}
}
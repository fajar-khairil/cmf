<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
	protected $app = null;

	public function setApplication(\Pimple\Container $app)
	{
		$this->app = $app;
	}

	public function getApplication()
	{
		if( null === $this->app )
			throw new \RuntimeException('Application not set for this model.');

		return $this->app;
	}
}
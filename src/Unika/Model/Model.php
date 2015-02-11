<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Model;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
	protected static $app = null;

	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);
		static::$app = \Unika\Application::instance();
	}
}
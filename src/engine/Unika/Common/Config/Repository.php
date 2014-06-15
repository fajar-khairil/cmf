<?php
/**
 *  This file is part of the Unika-CMF project.
 *  Config Repository extends Illuminate\Config\Repository
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

namespace Unika\Common\Config;

class Repository extends \Illuminate\Config\Repository
{
	public function set($key, $value)
	{
		parent::set($key,$value);
		$this->loader->afterSet($this->getEnvironment(),$key,$value);
	}
}
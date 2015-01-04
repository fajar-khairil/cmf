<?php 
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika;


class Util extends \utilphp\util { 
	
	/**
	 *	@param string $class className
	 *	@param string $interfaces interfaces to check 
	 *
	 *	@return boolean 
	 */
	public static function classImplements($class,$interface)
	{
		$intfs = class_implements($class);

		return in_array($interface, $intfs);
	}
}
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

     
    public static function boolToStr($value)
    {
    	return ( True === (bool)$value ) ? 'True' : 'False';
    }

    public static function toPrice($s)
    {
	    // convert "," to "."
	    $s = str_replace(',', '.', $s);

	    // remove everything except numbers and dot "."
	    $s = preg_replace("/[^0-9\.]/", "", $s);

	    // remove all seperators from first part and keep the end
	    $s = str_replace('.', '',substr($s, 0, -3)) . substr($s, -3);

	    // return float
	    return (float) $s;
    }
}
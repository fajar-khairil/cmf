<?php 
/**
 *	This file is part of the UnikaCMF project
 *	this class at as Factory for Valitron\Validator	
 *
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Helper;

class Validator 
{ 
    /**
     * Setup validation
     *
     * @param  array                     $data
     * @param  array                     $fields
     * @param  string                    $lang
     * @param  string                    $langDir
     * @throws \InvalidArgumentException
     * @return Valitron\Validator
     */
	public function create($data, $fields = array(), $lang = null, $langDir = null)
	{
		// @todo apply $lang and $langDir for our localization system
		return new \Valitron\Validation($data,$fields,$lang,$langDir);
	}
}
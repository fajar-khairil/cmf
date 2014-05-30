<?php
/**
 *
 *  Array ConfigReader
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */
namespace Unika\Config;

class NativeReader implements ReaderInterface
{
	public static function resolve($file)
	{
		$result = require $file;
		if( !is_array($result) ){
			throw new \RuntimeException('Invalid Config File, Config Must return array.');
		}

		return $result;
	}	
}

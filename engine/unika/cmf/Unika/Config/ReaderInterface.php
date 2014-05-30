<?php
/**
 *
 *  Reader Interface
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */
namespace Unika\Config;

Interface ReaderInterface
{
	/**
	 *	@param $file file to parse
	 *	@return array
	 */
	public static function resolve($file);
}
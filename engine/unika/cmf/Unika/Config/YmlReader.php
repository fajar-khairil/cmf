<?php
/**
 *
 *  Yml Config Reader
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */
namespace Unika\Config;

class YmlReader implements ReaderInterface
{
	public static function resolve($file)
	{
		
		return \Symfony\Component\Yaml\Yaml::parse($file);
	}	
}

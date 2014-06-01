<?php
/**
 *	This file is part of the Unika-CMF project.
 *	general purpose static function
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika;

 class Util
 {
 	public static function isWindows()
 	{
 		return (DIRECTORY_SEPARATOR === '\\');
 	}

 	public static function isUnix()
 	{
 		return (DIRECTORY_SEPARATOR === '/');
 	}
 }
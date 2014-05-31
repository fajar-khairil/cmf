<?php
/**
 *	Unika-CMF Project
 *	Store \Unika\Application on this static class
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika;

class Bag
{
    public static $BACKEND_URI = 'administrator';
    public static $ENGINE_PATH = '/';
    public static $BASE_URL = '/';
    
	protected static $instance = null;

	//return Unika\Application
	public static function instance()
	{
		if( static::$instance === null )
		{
			static::$instance = new Application();
		}

		return static::$instance;
	}
}
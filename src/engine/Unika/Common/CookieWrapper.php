<?php
/**
 *	This file is part of the Unika-CMF project.
 *	CookieWrapper
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Common;

class CookieWrapper
{
	protected $pp;

	public function __construct(\Application $app)
	{
		$this->app = $app;
	}

	public function cookie( $cookies )
	{
        if( $cookies === null )
        {
            $cookies = $this->app['config']['cookie.default'];
        }
        elseif( is_string($cookies) )
        {
             $cookies = $this->app['config']['cookie.'.$cookies];   
        }
        
        if( !is_array( $cookies ) )
        {
            throw new \InvalidArgumentException('invalid arguments.');
        }
        return new \Symfony\Component\HttpFoundation\Cookie(
            $cookies['name'],
            $cookies['value'],
            $cookies['expired'],               
            $cookies['path'],     
            $cookies['domain'],         
            $cookies['secure'],
            $cookies['httpOnly']
        );
	}
}
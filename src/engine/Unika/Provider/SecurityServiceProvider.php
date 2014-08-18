<?php
/**
 *	This file is part of the Unika-CMF project.
 *  Security Service Provider
 *
 *  @license MIT
 *  @author Fajar Khairil
 */

namespace Unika\Provider;

use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;

class SecurityServiceProvider implements ServiceProviderInterface,BootableProviderInterface
{

    public function register(\Pimple\Container $app)
    {
        $app['security.util'] = new \Unika\Security\Util($app);

        $default_auth = $app['config']['auth.driver']; 
        switch ( strtolower($default_auth) ) 
        {
        	case 'eloquent':
        		$app['auth'] = new \Unika\Security\Authentication\Eloquent\Auth($app);
        		break;      	
        	default:
        		throw new \RuntimeException($default_auth.' not implemented.');
        		break;
        }
    }  

 	public function boot(\Silex\Application $app)
 	{
         if( $app['config']['auth.guard_enabled'] === True )
        {
	        $guard = new \Unika\Security\Authentication\AuthGuard($app);
	        $guard->RegisterListener();   	
	    
	        $app->before(function($request) use($guard,$app)
	        {
	        	if( $guard->isBlocked($request->getClientIp()) )
	        	{
	        		$app->abort(403,'Blocked IP Address');
	        	}
	        });
        }  
 	}  
}
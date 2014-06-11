<?php

namespace Unika\Provider;

use Silex\ServiceProviderInterface;

class AuthGuardServiceProvider implements ServiceProviderInterface
{

    public function register(\Silex\Application $app)
    {
        $guard = new \Unika\Security\Eloquent\AuthGuard($app);
        $guard->RegisterListener();   	
    
        $app->before(function($request) use($guard,$app){
        	if( $guard->isBlocked($request->getClientIp()) )
        	{
        		$app->abort(403,'Blocked IP Address');
        	}
        });
    }   

    public function boot(\Silex\Application $app)
    {
        
    }
}
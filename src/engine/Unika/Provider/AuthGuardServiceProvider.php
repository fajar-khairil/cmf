<?php
/**
 *  This file is part of the Unika-CMF project.
 *  AuthGuardProvider prevent brute force and other security issue
 *
 *  @license MIT
 *  @author Fajar Khairil
 */

namespace Unika\Provider;

use Pimple\ServiceProviderInterface;

class AuthGuardServiceProvider implements ServiceProviderInterface
{

    public function register(\Pimple\Container $app)
    {
        $guard = new \Unika\Security\Authentication\AuthGuard($app);
        $guard->RegisterListener();   	
    
        $app->before(function($request) use($guard,$app){
        	if( $guard->isBlocked($request->getClientIp()) )
        	{
        		$app->abort(403,'Blocked IP Address');
        	}
        });
    }   
}
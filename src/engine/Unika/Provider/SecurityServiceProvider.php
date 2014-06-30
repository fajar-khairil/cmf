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

class SecurityServiceProvider implements ServiceProviderInterface
{

    public function register(\Pimple\Container $app)
    {
        $app['security.util'] = new \Unika\Security\Util($app);
        $app['auth'] = new \Unika\Security\Authentication\Eloquent\Auth($app);

        if( $app['config']['auth.guard_enabled'] === True ){
            $app->register(new \Unika\Provider\AuthGuardServiceProvider);
        }  
    }  
}
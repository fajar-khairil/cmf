<?php
/**
 *
 *  Bring Some Illuminate(L4) components to silex
 *
 *  @license MIT
 *  @author Fajar Khairil
 */

namespace Unika\Provider;

use Pimple\ServiceProviderInterface;

class AuthServiceProvider implements ServiceProviderInterface
{

    public function register(\Pimple\Container $app)
    {
        $app['security.util'] = new \Unika\Security\Util($app);
        $app['auth'] = new \Unika\Security\Eloquent\Auth($app);

        if( $app['config']['auth.guard_enabled'] === True ){
            $app->register(new \Unika\Provider\AuthGuardServiceProvider);
        }  
    }  
}
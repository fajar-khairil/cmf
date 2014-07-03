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
use Silex\Api\BootableProviderInterface;

class AuthorizationServiceProvider implements ServiceProviderInterface,BootableProviderInterface
{
    public function register(\Pimple\Container $app)
	{

	}

    public function boot(Application $app)
    {
		//register eloquent resource saving event
		$resource_object = $app['config']['acl.eloquent.resource_class'];
		$resource_object::saving(function($instance){
			$instance->name = str_replace(' ', '_', $instance->name);
		});

		//register eloquent role saving event
		$role_object = $app['config']['acl.eloquent.role_class'];
		$role_object::saving(function($instance){
			$instance->name = str_replace(' ', '_', $instance->name);
		});    	
    }
}
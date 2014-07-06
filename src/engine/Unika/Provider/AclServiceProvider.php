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

class AclServiceProvider implements ServiceProviderInterface,BootableProviderInterface
{
    public function register(\Pimple\Container $app)
	{
		$acl = new \Unika\Security\Authorization\Acl(
			new \Unika\Security\Authorization\Eloquent\RoleRegistry($app),
			new \Unika\Security\Authorization\Eloquent\ResourceRegistry($app),
			new \Unika\Security\Authorization\Eloquent\Acl($app)
		);	

		//set Auth to Acl
		$acl->setAuth($app['auth']);

		$app['acl'] = $acl;
	}

    public function boot(\Silex\Application $app)
    {
		if( ! isset($app['capsule']) ) {
			throw new \RuntimeException(__CLASS__.' need capsule manager. dependecy not satisfied.');
		}

		//register eloquent resource saving event
		$resource_object = $app['config']['acl.eloquent.resource_implementation'];
		$resource_object::saving(function($instance){
			$instance->name = preg_replace('/[.," "]/', '_', $instance->name);
		});
		//register eloquent role saving event
		$role_object = $app['config']['acl.eloquent.role_class'];
		$role_object::saving(function($instance){
			$instance->name = preg_replace('/[.," "]/', '_', $instance->name);
		});   	
    }
}
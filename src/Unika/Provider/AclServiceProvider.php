<?php
/**
 * This file is part of the Unika-CMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

namespace Unika\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Unika\Security\Authorization\ResourceRegistryInterface;
use Unika\Security\Authorization\RoleRegistryInterface;

class AclServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
    {
    	$defaultImpl = $app->config['acl.default'];
    	switch ($defaultImpl) 
    	{
    		case 'Database':
    			$app['acl'] = new \Unika\Security\Authorization\Acl(
    					new \Unika\Security\Authorization\Driver\Database\RoleRegistry($app),
    					new \Unika\Security\Authorization\Driver\Database\ResourceRegistry($app),
    					new \Unika\Security\Authorization\Driver\Database\AclRegistry($app)
    				);
    			break;
    		
    		default:
    			throw new \RuntimeException(sprintf('%s is invalid Acl Implementation name',$defaultImpl));
    			break;
    	}
    }
}
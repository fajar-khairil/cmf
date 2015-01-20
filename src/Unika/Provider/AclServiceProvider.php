<?php
/**
 * This file is part of the UnikaCMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

namespace Unika\Provider;

use Pimple\Container;
use Unika\Interfaces\ServiceProviderInterface;
use Unika\Security\Authorization\ResourceRegistryInterface;
use Unika\Security\Authorization\RoleRegistryInterface;
use Unika\Interfaces\CommandProviderInterface;
use Unika\Console;

class AclServiceProvider implements ServiceProviderInterface,CommandProviderInterface
{
	public function register(Container $app)
    {
    	$defaultImpl = $app->config('acl.default');

    	switch ($defaultImpl) 
    	{
    		case 'Database':
                $this->registerAclDatabase($app);
    			break;
    		
    		default:
                if( $app['debug'] === False )
                {
                    $this->registerAclDatabase($app);
                    $app['logger']->addError($defaultImpl.' : Invalid implementation name of Acl Implementation.  use Database as fallback.');
                }
                else
                {
                    $app['logger']->addCritical($defaultImpl.' : Invalid implementation name of Acl Implementation.');
                    throw new \RuntimeException(sprintf('%s is invalid Acl Implementation name',$defaultImpl));
    			}
                break;
    	}
    }

    /**
     *
     *  register command if any
     */
    public function addCommand(Console $app)
    {
        $app->add(new \Unika\Command\ACL\InstallCommand());
    }

    protected function registerAclDatabase(Container $app)
    {
        $app['acl'] = new \Unika\Security\Authorization\Acl(
                new \Unika\Security\Authorization\Driver\Database\RoleRegistry($app),
                new \Unika\Security\Authorization\Driver\Database\ResourceRegistry($app),
                new \Unika\Security\Authorization\Driver\Database\AclDriver($app)
            );
    }

    /**
     *
     *  return description of provider
     */
    public function getDescription()
    {
        return 'Access Control Lists Provider';
    }

    /**
     *
     *  return array of service with each description
     */
    public function getServices()
    {
        return array(
            'acl'   =>  'Unika\Security\Authorization\Acl'
        );
    }

    /**
     *
     *  return an array('author' => '','license' => '','url' => '');
     */
    public function getInfo()
    {
        return array(
            'name'      => 'ACL',
            'author'    => 'Fajar Khairil',
            'license'   => 'MIT',
            'url'       => 'http://www.unikacreative.com/',
            'version'   => '0.1'
        );
    }
}
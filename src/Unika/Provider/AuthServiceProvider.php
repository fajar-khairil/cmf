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
use Unika\Interfaces\CommandProviderInterface;
use Unika\Console;

class AuthServiceProvider implements ServiceProviderInterface,CommandProviderInterface
{
	public function register(Container $app)
    {
    	$app['auth'] = function($app){
    		
    		$authDriverClass = $app->config('auth.default');
    		$authDriver = null;

    		switch ($authDriverClass) {
    			case 'database':
    				$authDriver = new \Unika\Security\Authentication\Driver\AuthDatabase(
                        $app,
                        $app->config('auth.drivers.connection_name'),
                        $app->config('auth.guard.active')
                    );
    				break;  			
    			default:
    				throw new \RuntimeException($authDriverClass.' not yet implemented.');
    				break;
    		}
    		
    		$auth = new \Unika\Security\Authentication\Auth(
                $app,
    			$authDriver,
    			$app['session']
    		);

            $auth->setDefaultRememberTimeout( $app->config('auth.remember_me.default_timeout',30) );
            $auth->setCache($app['cache']);

            return $auth;
    	};
    }

    /**
     *
     *  register command if any
     */
    public function addCommand(Console $app)
    {
        $app->add(new \Unika\Command\Auth\InstallCommand('auth:install'));
    }

    /**
     *
     *  return description of provider
     */
    public function getDescription()
    {
        return 'Authentication Service Provider';
    }

    /**
     *
     *  return array of service with each description
     */
    public function getServices()
    {
        return array(
            'auth'  => '\Unika\Security\Authentication\Auth'
        );
    }

    /**
     *
     *  return an array('author' => '','license' => '','url' => '');
     */
    public function getInfo()
    {
        return array(
            'author'    => 'Fajar Khairil',
            'license'   => 'MIT',
            'url'       => 'http://www.unikacreative.com/',
            'version'   => '0.1'
        );
    }
}
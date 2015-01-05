<?php
/**
 * This file is part of the UnikaCMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

namespace Unika\Provider;

use Pimple\Container;
use Unika\ServiceProviderInterface;

class AuthServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
    {
    	$app['auth'] = function($app){
    		
    		$authDriverClass = $app->config('auth.driver');
    		$authDriver = null;

    		switch ($authDriverClass) {
    			case 'database':
    				$authDriver = new \Unika\Security\Authentication\Driver\AuthDatabase($app,$app->config('auth.guard.active'));
    				break;
    			
    			default:
    				throw new \RuntimeException($authDriverClass.' not yet implemented.');
    				break;
    		}
    		
    		$auth = new \Unika\Security\Authentication\Auth(
    			$authDriver,
    			$app['session']
    		);

            $auth->setApplication($app);
            $auth->setCache($app['cache']);

            return $auth;
    	};
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
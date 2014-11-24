<?php
/**
 *  This file is part of the Unika-CMF project.
 *	Capsule(Eloquent) Service Provider
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Provider;

use Pimple\ServiceProviderInterface;

class CapsuleServiceProvider implements ServiceProviderInterface
{
	public function register(\Pimple\Container $app)
	{
		$app['capsule'] = function($app){
			$Capsule = new \Illuminate\Database\Capsule\Manager();
			$Capsule->setAsGlobal();
			$defaultConn = $app['config']->get('database.default','mysqlconn');
			$Capsule->addConnection(
				$app['config']['database'][$defaultConn]
			);

			$Capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher($app['Illuminate.container']));

			$Capsule->setCacheManager( $app['cache_manager'] );			
			return $Capsule;
		};
		
		$app['capsule']->bootEloquent();

        $app['setting'] = function($app){
            return new \Unika\Common\Config\Repository( 
                new \Unika\Common\Config\Eloquent(
                    $app, 
                    $app['capsule'],
                    $app['cache']
                ), 
                \Application::detectEnvirontment()
            );
        };   		
	}
}
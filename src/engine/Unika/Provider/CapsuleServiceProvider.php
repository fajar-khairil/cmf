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
			$Capsule->addConnection(
				$app['config']->get('database.default')
			);

			$dispatcher = new \Illuminate\Events\Dispatcher($app['Illuminate.container']);
			$Capsule->setEventDispatcher($dispatcher);

			$Capsule->setCacheManager( $app['cache_manager'] );

			return $Capsule;
		};
	}
}
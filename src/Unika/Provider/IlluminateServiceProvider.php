<?php
/**
 *
 *	Bring Some Illuminate(L4) components to silex
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Provider;

use Silex\ServiceProviderInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

class IlluminateServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(\Silex\Application $app)
    {
        //Illuminate Container
        $app['Illuminate.container'] = $app->share(function($app){
           $container = new \Illuminate\Container\Container(); 

           $container['config'] = $app['config'];

           return $container;
        });

        $container = $app['Illuminate.container'];
        $container->singleton('memcached.connector',function(){
            return new \Illuminate\Cache\MemcachedConnector();
        });          

        // BEGIN Cache
        if( !isset($app['config']['cache.default']) )
        {
            if( APC_PRESENT === TRUE )
                $app['config']['cache.default'] = 'Apc';
            else
                $app['config']['cache.default'] = 'File';
        }

        $app['CacheManager'] = $app->share(function($app) use($container){

            $container['config']['cache.path'] = $app['config']['app.tmp_dir'].DIRECTORY_SEPARATOR.'cache';
            
            if( class_exists('\\Memcached') )
            {
               $container['config']['cache.memcached'] = $app['config']->get('cache.Memcached');

        	   $container['memcached.connector']->connect( $container['config']['cache.memcached'] );
            }

            $CacheManager = new \Illuminate\Cache\CacheManager( $container );
        
            $CacheManager->setPrefix( $app['config']->get('cache.prefix') );
            $CacheManager->setDefaultDriver( $app['config']['cache.default'] );

            return $CacheManager;
        });

        $app['cache'] = $app->share(function($app){
            return $app['CacheManager']->driver();
        });
        //END Cache

        //BEGIN Eqloquent

		$app['Capsule'] = $app->share(function($app){
			$Capsule = new Capsule();
			$Capsule->setAsGlobal();
			$Capsule->addConnection(
				$app['config']->get('database.default')
			);

			$dispatcher = new \Illuminate\Events\Dispatcher($app['Illuminate.container']);
			$Capsule->setEventDispatcher($dispatcher);

			$Capsule->setCacheManager( $app['CacheManager'] );

			return $Capsule;
		});      
		//END Eloquent  
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(\Silex\Application $app)
    {
        
    }	
}
<?php
/**
 *  This file is part of the Unika-CMF project.
 *	Bring Some Illuminate(L4) components
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Provider;

use Pimple\ServiceProviderInterface;
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
    public function register(\Pimple\Container $app)
    {
        $app['Illuminate.files'] = function(){
            return new \Illuminate\Filesystem\Filesystem();
        };   

        $app['config'] = function($app){
            return new \Illuminate\Config\Repository( 
                new \Unika\Common\Config\File( 
                    $app['Illuminate.files'],
                    \Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'config' 
                ), 
                \Application::detectEnvirontment()
            );
        };

        //Illuminate Container
        $app['Illuminate.container'] = function($app){
           $container = new \Illuminate\Container\Container(); 

           $container['config'] = $app['config'];

           return $container;
        };

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

        $app['cache_manager'] = function($app) use($container){

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
        };

        $app['cache'] = $app['cache_manager']->driver();
        //END Cache

        //BEGIN Eloquent
		$app['capsule'] = function($app){
			$Capsule = new Capsule();
			$Capsule->setAsGlobal();
			$Capsule->addConnection(
				$app['config']->get('database.default')
			);

			$dispatcher = new \Illuminate\Events\Dispatcher($app['Illuminate.container']);
			$Capsule->setEventDispatcher($dispatcher);

			$Capsule->setCacheManager( $app['cache_manager'] );

			return $Capsule;
		};      
		//END Eloquent  

        $app['Illuminate.events'] = new \Illuminate\Events\Dispatcher($app['Illuminate.container']);

        //BEGIN Blade
        $app['Illuminate.blade'] = function($app){
            $blade_config = $app['config']['view.blade'];

            $blade = new \Illuminate\View\Compilers\BladeCompiler($app['Illuminate.files'],$blade_config['cache']);
            $blade->setContentTags($blade_config['content_tags'][0],$blade_config['content_tags'][1]);
            return $blade;
        };
        //END Blade

        $app['view.finder'] = function($app){
            $finder = new \Unika\Ext\ViewFinder($app['Illuminate.files'], $app['config']['view.paths']);
            return $finder;
        };         
    }
}
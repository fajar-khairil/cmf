<?php
/**
 *  This file is part of the Unika-CMF project.
 *	Cache Service Provider
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Provider;

use Pimple\ServiceProviderInterface;

class CacheServiceProvider implements ServiceProviderInterface
{
	public function register(\Pimple\Container $app)
	{
        if( !isset($app['config']['cache.default']) )
        {
            if( APC_PRESENT === TRUE )
                $app['config']['cache.default'] = 'Apc';
            else
                $app['config']['cache.default'] = 'File';
        }

        $container = $app['Illuminate.container'];

        $container->singleton('files',function() use($app){
            return $app['Illuminate.files'];
        });

        $container->singleton('memcached.connector',function(){
            return new \Illuminate\Cache\MemcachedConnector();
        });   

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
	}
}
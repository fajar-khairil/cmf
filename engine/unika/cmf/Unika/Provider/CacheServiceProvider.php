<?php
/**
 *
 *  CacheServiceProvider cache made easy
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */
namespace Unika\Provider;

use Silex\ServiceProviderInterface;

class CacheServiceProvider implements ServiceProviderInterface
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
    	if( !isset($app['cache.default']) )
    	{
    		if( APC_PRESENT === TRUE )
    			$app['cache.default'] = 'Apc';
    		else
    			$app['cache.default'] = 'File';
    	}
        
        $app['cache.manager'] = $app->share(function($app){
            return new \Unika\CacheManager($app);
        });
    	$app['cache'] =  $app['cache.manager']->getCache($app['cache.default']);
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
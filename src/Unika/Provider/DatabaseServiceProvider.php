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
use ORM;

class DatabaseServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
    {
    	$configs = $app->config('database');
    	$default_connection = \Illuminate\Support\Arr::pull($configs,'default');
    	foreach ($configs as $con_name=>$config) 
    	{
    		$dsn = $config['driver'].':host='.$config['host'].';dbname='.$config['database'];

    		//look for default connection
    		$connection_name = $con_name;
    		if( $default_connection == $con_name ){
    			$connection_name = ORM::DEFAULT_CONNECTION;
    		}

    		//set dsn
    		ORM::configure($dsn,null,$connection_name);

    		foreach ($config as $key=>$value)
    		{        
                if( $value === null ) continue;
    			ORM::configure([$key => $value] , null , $connection_name);
    		}

            // set cache
            $cache_driver = ORM::get_config('cache_driver',$connection_name);
            if( $cache_driver != 'Database' ) //of course no database cache allowed
            {
                $cache = $app['cache.factory']($cache_driver);
                //$cache = $app['cache'];

                ORM::configure('cache_query_result', function ($cache_key, $value, $table_name, $connection_name) use ($cache,$app) {
                    $cache->add($cache_key, serialize($value) , (int)$app->config('database.'.$connection_name.'.cache_expiration'));
                });

                ORM::configure('check_query_cache', function ($cache_key, $table_name, $connection_name) use ($cache) {
                    if( $cache->has($cache_key) ){
                       return @unserialize($cache->get($cache_key));
                    } else {
                        return false;
                    }
                });

                ORM::configure('clear_cache', function ($table_name, $connection_name) use ($cache) {
                     $cache->getStore()->flush();
                });

                ORM::configure('create_cache_key', function ($query, $parameters, $table_name, $connection_name) {
                    $parameter_string = join(',', $parameters);
                    $key = $query . ':' . $parameter_string;
                    $my_key = 'dbcache_'.crc32($key);
                    return $my_key;
                });                
            }

            ORM::configure(['return_result_sets' => true],null,$connection_name);

    		ORM::configure('logger',function($log_string, $query_time){
				$app['logger']->addDebug('SQL : ' . $log_string . ' in ' . $query_time); 			
    		},$connection_name);
    	}
    }
}
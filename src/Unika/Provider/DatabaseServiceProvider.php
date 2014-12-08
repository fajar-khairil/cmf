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
    	$configs = $app->config['database'];
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
    			ORM::configure([$key => $value],null,$connection_name);
    		}

            ORM::configure(['return_result_sets' => true],null,$connection_name);

    		ORM::configure('logger',function($log_string, $query_time){
				$app['logger']->addDebug('SQL : ' . $log_string . ' in ' . $query_time); 			
    		},$connection_name);
    	}
    }
}
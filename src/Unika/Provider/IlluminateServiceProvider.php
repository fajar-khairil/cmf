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

class IlluminateServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
    {	
    	$app['Illuminate.container'] = new \Illuminate\Container\Container();
    	$app['Illuminate.filesystem'] = new \Illuminate\Filesystem\Filesystem();
    	$app['Illuminate.memcached'] = new \Illuminate\Cache\MemcachedConnector();
    	$app['Illuminate.events'] = new \Illuminate\Events\Dispatcher($app['Illuminate.container']);
    }	
}
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

class CacheServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
    {
    	$app['cache.manager'] = new \Unika\Ext\CacheManager($app);
    	$app['cache'] = function($app){
    		return new \Illuminate\Cache\Repository( $app['cache.manager']->driver() );
    	};
   
    }
}
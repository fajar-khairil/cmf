<?php
/**
 * This file is part of the UnikaCMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

namespace Unika\Provider;

use Pimple\Container;
use Unika\ServiceProviderInterface;

class CacheServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
    {
    	$app['cache.manager'] = new \Unika\Ext\CacheManager($app);
    	$app['cache'] = function($app){
    		return new \Illuminate\Cache\Repository( $app['cache.manager']->driver() );
    	};
   
    }

    /**
     *
     *  return description of provider
     */
    public function getDescription()
    {
        return 'Cache Service Provider with various backend';
    }

    /**
     *
     *  return array of service with each description
     */
    public function getServices()
    {
        return array(
            'cache'   		=>  'Illuminate\Cache\Repository',
            'cache.manager'	=>	'Unika\Ext\CacheManager'
        );
    }

    /**
     *
     *  return an array('author' => '','license' => '','url' => '');
     */
    public function getInfo()
    {
        return array(
            'author'    => 'Fajar Khairil',
            'license'   => 'MIT',
            'url'       => 'http://www.unikacreative.com/',
            'version'	=> '0.1'
        );
    }
}
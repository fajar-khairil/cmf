<?php
/**
 * This file is part of the UnikaCMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

namespace Unika\Provider;

use Pimple\Container;
use Unika\Interfaces\ServiceProviderInterface;

class IlluminateServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
    {	
    	$app['Illuminate.container'] = new \Illuminate\Container\Container();
    	$app['Illuminate.filesystem'] = new \Illuminate\Filesystem\Filesystem();
    	$app['Illuminate.memcached'] = new \Illuminate\Cache\MemcachedConnector();
    	$app['Illuminate.events'] = new \Illuminate\Events\Dispatcher($app['Illuminate.container']);
    }	

    /**
     *
     *  return description of provider
     */
    public function getDescription()
    {
        return 'Ussefull Illuminate Components';
    }

    /**
     *
     *  return array of service with each description
     */
    public function getServices()
    {
        return array(
            'Illuminate.container'	=> 'Illuminate\Container\Container',
            'Illuminate.filesystem' => 'Illuminate\Filesystem\Filesystem', 
            'Illuminate.memcached'	=> 'Illuminate\Cache\MemcachedConnector',
            'Illuminate.events'		=> 'Illuminate\Events\Dispatcher'
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
            'version'   => '0.1'
        );
    }
}
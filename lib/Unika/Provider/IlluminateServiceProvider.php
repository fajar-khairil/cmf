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
        
        $app['paginator'] = function($app){
            $paginator = new \Illuminate\Pagination\Factory(
                \Illuminate\Http\Request::createFromBase($app['request_stack']->getCurrentRequest()), 
                $app['view'], 
                $app['translator']
            );

            $paginator->setViewName('pagination::simple');

            return $paginator;
        };

        $app['Illuminate.container']->bindShared('paginator',function($illuminateContainer) use($app){
            return $app['paginator'];
        });
    }	

    /**
     *
     *  return description of provider
     */
    public function getDescription()
    {
        return 'Ussefull Illuminate(laravel) Components';
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
            'Illuminate.events'		=> 'Illuminate\Events\Dispatcher',
            'paginator'             => 'Illuminate\Pagination\Factory'
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
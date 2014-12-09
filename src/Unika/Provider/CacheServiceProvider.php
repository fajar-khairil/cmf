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
	protected $config;
	protected $app;

	public function register(Container $app)
    {
    	$this->app = $app;
    	$this->config = $this->app->config('cache');
    	$app['cache'] = function($app){
    		return new \Illuminate\Cache\Repository( $this->getDefaultStore($app) );
    	};

    	$self = $this;
    	$app['cache.factory'] = $app->protect(function($storeName) use($app,$self){
    		//dd($this->config);
    		// its ugly
    		//$self = new static();
	    	//$this->app = $app;
	    	//$this->config = $this->app->config('cache');

    		return new \Illuminate\Cache\Repository( $self->getDefaultStore($app,$storeName) ); 
    	});
    }

    protected function getDefaultStore(Container $app,$storeImpl = null)
    {
    	if( is_string($storeImpl) )
    		$defaultStore = 'create'.$storeImpl.'Driver';
    	else
    		$defaultStore = 'create'.$app->config('cache.default').'Driver';

    	return $this->$defaultStore();
    }

	protected function createApcDriver()
	{
		return new ApcStore(new \Illuminate\Cache\ApcWrapper, $config['prefix']);
	}

	protected function createArrayDriver()
	{
		return new \Illuminate\Cache\ArrayStore;
	}

	protected function createFileDriver()
	{
		$path = $this->config['File']['dir'];

		return new \Illuminate\Cache\FileStore(new \Illuminate\Filesystem\Filesystem, $path);
	}

	protected function createNullDriver()
	{
		return new \Illuminate\Cache\NullStore;
	}

	protected function createWincacheDriver()
	{
		return new \Illuminate\Cache\WinCacheStore( $this->getPrefixFor('Wincache') );
	}

	protected function createXcacheDriver()
	{
		return new \Illuminate\Cache\XCacheStore( $this->getPrefixFor('Xcache') );
	}

	protected function createMemcachedDriver()
	{
		$memcached = $this->app['Illuminate.Memcached']->connect( $this->config['Memcached'] );

		return new \Illuminate\Cache\MemcachedStore($memcached, $this->getPrefixFor('Memcached'));
	}

	protected function createRedisDriver()
	{
		if( !class_exists('\Illuminate\Redis\Database') )
		{
			throw new \RuntimeException('\Illuminate\Redis\Database\Redis class not found , please install it to use redis as cache backend.');
		}

		return new \Illuminate\Cache\RedisStore(new \Illuminate\Redis\Database( $this->config['Redis']['default'] ), $this->getPrefixFor('Redis'));
	}

	protected function getPrefixFor($storeName = null)
	{
		if( $storeName === null )
			return $this->config['prefix'];

		return array_get($this->config,$storeName.'.prefix',$this->config['prefix']);
	}

}
<?php 
/**
 * This file is part of the Unika-CMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

namespace Unika\Ext;

class CacheManager extends \Illuminate\Cache\CacheManager
{
	protected $app;

	public function __construct(\Unika\Application $app)
	{	
		$this->app = $app;
	}

	protected function createApcDriver()
	{
		return new \Illuminate\Cache\ApcStore(new \Illuminate\Cache\ApcWrapper, $this->getPrefix() );
	}

	protected function createArrayDriver()
	{
		return new \Illuminate\Cache\ArrayStore;
	}

	protected function createFileDriver()
	{
		return new \Illuminate\Cache\FileStore($this->app['Illuminate.filesystem'], $this->app->config('cache.File.dir') );
	}

	protected function createNullDriver()
	{
		return new \Illuminate\Cache\NullStore;
	}

	protected function createWincacheDriver()
	{
		return new \Illuminate\Cache\WinCacheStore( $this->getPrefix() );
	}

	protected function createXcacheDriver()
	{
		return new \Illuminate\Cache\XCacheStore( $this->getPrefix() );
	}

	protected function createMemcachedDriver()
	{
		$memcached = $this->app['Illuminate.memcached']->connect( $this->app->config('cache.Memcached') );

		return new \Illuminate\Cache\MemcachedStore($memcached, $this->getPrefix() );
	}

	protected function createRedisDriver()
	{
		if( !class_exists('\Illuminate\Redis\Database') )
		{
			$errMsg = '\Illuminate\Redis\Database\Redis class not found , please install it to use redis as cache backend.';
			$this->app['logger']->addCritical($errMsg);
			throw new \RuntimeException($errMsg);
		}

		return new \Illuminate\Cache\RedisStore(
			new \Illuminate\Redis\Database( $this->app->config('database.Redis.default') ), $this->getPrefix() 
		);
	}
}
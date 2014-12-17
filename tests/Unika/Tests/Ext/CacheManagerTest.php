<?php
/**
 * This file is part of the Unika-CMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

require_once TEST_ROOT.'/AppTestCase.php';

class CacheMangerTest extends AppTestCase
{
	protected $cacheManager;

    protected function setUp()
    {
    	parent::setUp();
    	$this->app['config']['cache.driver'] = 'Apc';
    	$this->cacheManager = $this->app['cache.manager'];
    }

    public function testValidClass()
    {
        $this->assertTrue( $this->cacheManager instanceof \Unika\Ext\CacheManager );
    }

    public function testGetDefaultDriver()
    {
  		$this->assertEquals( 'Apc' , $this->cacheManager->getDefaultDriver() );
    }

    public function testGetDriver()
    {
        $this->assertTrue( $this->cacheManager->driver() instanceof Illuminate\Cache\ApcStore );  
        $this->assertTrue( $this->cacheManager->driver('File') instanceof Illuminate\Cache\FileStore ); 
        $this->assertTrue( $this->cacheManager->driver('Apc') instanceof Illuminate\Cache\ApcStore );
        $this->assertTrue( $this->cacheManager->driver('Array') instanceof Illuminate\Cache\ArrayStore );
        $this->assertTrue( $this->cacheManager->driver('Null') instanceof Illuminate\Cache\NullStore );
        $this->assertTrue( $this->cacheManager->driver('Wincache') instanceof Illuminate\Cache\WincacheStore );
        $this->assertTrue( $this->cacheManager->driver('Xcache') instanceof Illuminate\Cache\XcacheStore );
        $this->assertTrue( $this->cacheManager->driver('Memcached') instanceof Illuminate\Cache\MemcachedStore );

        if( class_exists('\Illuminate\Redis\Database') )
        	$this->assertTrue( $this->cacheManager->driver('Redis') instanceof Illuminate\Cache\RedisStore );
    }

    public function testCacheProvider()
    {
    	$this->assertTrue( $this->app['cache'] instanceof \Illuminate\Cache\Repository );
    }
}
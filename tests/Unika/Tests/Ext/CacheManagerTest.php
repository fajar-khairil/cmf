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
    	$this->cacheManager = $this->app['cache.manager'];
    }

    public function testValidClass()
    {
        $this->assertTrue( $this->cacheManager instanceof \Unika\Ext\CacheManager );
    }

    public function testGetDefaultDriver()
    {
  		$this->assertEquals( 'File' , $this->cacheManager->getDefaultDriver() );
    }

    public function testGetDriver()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );    	
    }
}
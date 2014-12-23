<?php
/**
 * This file is part of the Unika-CMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

require_once TEST_ROOT.'/AppTestCase.php';

class AclTest extends AppTestCase
{
	protected $acl;
    
    protected function setUp()
    {
    	parent::setUp();
    	$this->acl = $this->app['acl'];
    }	
}
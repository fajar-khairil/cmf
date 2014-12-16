<?php
/**
 * This file is part of the Unika-CMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

abstract class AppTestCase extends \PHPUnit_Framework_TestCase
{
	protected $app;

    protected function setUp()
    {
    	$this->app = \Unika\Application::instance();
    }
}
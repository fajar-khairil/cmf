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
use Unika\Security\Authorization\ResourceRegistryInterface;
use Unika\Security\Authorization\RoleRegistryInterface;

class SymfonyServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
    {
    	$app['sf.finder'] = function($app){
            $finder = new \Symfony\Component\Finder\Finder();
            $finder->useBestAdapter();
        };
    }


    /**
     *
     *  return description of provider
     */
    public function getDescription()
    {
        return 'Shortcut for several Symfony Components';
    }

    /**
     *
     *  return array of service with each class
     */
    public function getServices()
    {
        return array(
            'sf.finder'   =>  'Symfony\Component\Finder\Finder'
        );
    }

    /**
     *
     *  return an array('author' => '','license' => '','url' => '');
     */
    public function getInfo()
    {
        return array(
            'name'      => 'Symfony Component Shorcut',
            'author'    => 'Fajar Khairil',
            'license'   => 'MIT',
            'url'       => 'http://www.unikacreative.com/',
            'version'   => '0.1'
        );
    }
}
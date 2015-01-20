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

class SymfonyServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
    {
    	$app['sf.finder'] = function($app){
            $finder = new \Symfony\Component\Finder\Finder();
            $finder->useBestAdapter();
            return $finder;
        };

        $app['sf.stopwatch'] = $app->factory(function(){
            return new \Symfony\Component\Stopwatch\Stopwatch();
        });
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
            'sf.finder'   =>  'Symfony\Component\Finder\Finder',
            'sf.stopwatch'   =>  'Symfony\Component\Stopwatch\Stopwatch'
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
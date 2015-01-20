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
use Unika\Interfaces\CommandProviderInterface;
use Unika\Console;

class DatabaseServiceProvider implements ServiceProviderInterface,CommandProviderInterface
{
	public function register(Container $app)
    {
        $app['database'] = function($app)
        {
            $Capsule = new \Illuminate\Database\Capsule\Manager($app['Illuminate.container']);

            $connections = $app->config('database.connections');
            $app['Illuminate.container']['config']['database.default'] = $app->config('database.default','master');

            foreach(  $connections as $connName => $connection )
            {         
                $Capsule->addConnection($connection,$connName);
            }

            $Capsule->setEventDispatcher( $app['Illuminate.events'] );
            $Capsule->setCacheManager( $app['cache.manager'] );         
            $Capsule->setAsGlobal();

            return $Capsule;
        };
        
        $app['database']->bootEloquent();
    }

    /**
     *
     *  register command if any
     */
    public function addCommand(Console $app)
    {
        $app->add(new \Unika\Command\Migrations\InstallCommand());
    }

    /**
     *
     *  return description of provider
     */
    public function getDescription()
    {
        return 'Database Service Provider';
    }

    /**
     *
     *  return array of service with each description
     */
    public function getServices()
    {
        return array(
            'database'    =>  'Illuminate\Database\Capsule\Manager'
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
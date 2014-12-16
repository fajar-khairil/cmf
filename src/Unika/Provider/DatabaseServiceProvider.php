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

class DatabaseServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
    {
        $app['database'] = function($app)
        {
            $Capsule = new \Illuminate\Database\Capsule\Manager();
            $Capsule->setAsGlobal();
            $defaultConn = $app['config']->get('database.driver','master');
            $Capsule->addConnection(
                $app['config']['database.connections'][$defaultConn]
            );
            $Capsule->setEventDispatcher( $app['Illuminate.events'] );
            $Capsule->setCacheManager( $app['cache.manager'] );         
            return $Capsule;
        };
        
        $app['database']->bootEloquent();

        $app['setting'] = function($app){
            return new \Unika\Common\Config\Repository( 
                new \Unika\Common\Config\Eloquent(
                    $app, 
                    $app['capsule'],
                    $app['cache']
                ), 
                \Application::detectEnvirontment()
            );
        };      
    }
}
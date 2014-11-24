<?php
/**
 *  This file is part of the Unika-CMF project.
 *	Bring the Core of Illuminate(L4) components to Application
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Provider;

use Pimple\ServiceProviderInterface;

class IlluminateServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(\Pimple\Container $app)
    {
        $app['Illuminate.files'] = function(){
            return new \Illuminate\Filesystem\Filesystem();
        };   

        $app['config'] = function($app){
            return new \Illuminate\Config\Repository( 
                new \Unika\Common\Config\File(
                    $app, 
                    $app['Illuminate.files'],
                    \Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'config' 
                ), 
                \Application::detectEnvirontment()
            );
        };              

        //Illuminate Container
        $app['Illuminate.container'] = function($app){
           $container = new \Illuminate\Container\Container(); 

           $container['config'] = $app['config'];

           return $container;
        };       

        $app['Illuminate.events'] = new \Illuminate\Events\Dispatcher($app['Illuminate.container']);            
    }
}
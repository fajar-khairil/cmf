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
use Unika\Console;

class ViewServiceProvider implements ServiceProviderInterface,\Unika\Interfaces\CommandProviderInterface
{
    public function register(Container $app)
    {
    	$app['view'] = function($app){
    		$paths = array($app['path.themes'].'/'.$app->config('app.default_theme'));
            
            $engineResolver = new \Illuminate\View\Engines\EngineResolver();

            $engineResolver->register('php',function(){
            	return new \Illuminate\View\Engines\PhpEngine();
            });

            $engineResolver->register('blade',function() use($app){
            	$cache_path = $app::$ROOT_DIR.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'views';              
                $blade = new \Illuminate\View\Compilers\BladeCompiler($app['Illuminate.filesystem'],$cache_path);
                return new \Illuminate\View\Engines\CompilerEngine($blade);
            });

            $viewFactory = new \Illuminate\View\Factory(
            	$engineResolver,
            	new \Illuminate\View\FileViewFinder(
            		$app['Illuminate.filesystem'],
            		$paths,
                    array('blade','php')
            	),
                $app['Illuminate.events']
            );

            $viewFactory->share('app',$app);
            $viewFactory->addExtension('blade','blade');
            $viewFactory->setContainer($app['Illuminate.container']);

            return $viewFactory;
    	};
    }

    public function addCommand(Console $app)
    {
        $app->add(new \Unika\Command\ViewCommand('view:flush'));
    }

    /**
     *
     *  return description of provider
     */
    public function getDescription()
    {
        return 'View Service Provider using blade';
    }

    /**
     *
     *  return array of service with each description
     */
    public function getServices()
    {
        return array(
            'view'  => 'Unika\Ext\ViewFactory'
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
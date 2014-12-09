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

class ViewServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
    	$app['view'] = function($app){
    		$paths = array($app::$ROOT_DIR.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$app->config('app.default_theme'));
            

            $engineResolver = new \Illuminate\View\Engines\EngineResolver();

            $engineResolver->register('php',function(){
            	return new \Illuminate\View\Engines\PhpEngine();
            });

            $engineResolver->register('blade',function() use($app){
            	$cache_path = $app::$ROOT_DIR.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'views';
                
                $blade = new \Illuminate\View\Compilers\BladeCompiler($app['illuminate.filesystem'],$cache_path);
                return new \Illuminate\View\Engines\CompilerEngine($blade);
            });

            $viewFactory = new \Unika\Ext\ViewFactory(
            	$engineResolver,
            	new \Illuminate\View\FileViewFinder(
            		$app['illuminate.filesystem'],
            		$paths,
                    array('blade','php')
            	)
            );

            $viewFactory->setContainer($app);

            return $viewFactory;
    	};
    }
}
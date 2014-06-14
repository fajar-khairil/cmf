<?php
/**
 *  This file is part of the Unika-CMF project.
 *	View Service Provider
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Provider;

use Pimple\ServiceProviderInterface;

class ViewServiceProvider implements ServiceProviderInterface
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
        $this->registerEngineResolver($app);
        $this->registerFactory($app);

        $app['view'] = function($app){
            return new \Unika\Common\ViewWrapper($app);
        };
    }

    protected function registerEngineResolver($app)
    {
        $resolver = $app['view.resolver'] = new \Illuminate\View\Engines\EngineResolver();

        foreach (array('twig','php', 'blade') as $engine)
        {
            $this->{'register'.ucfirst($engine).'Engine'}($app,$resolver);
        }

        return $resolver;
    }

    /**
     * Register the PHP engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    protected function registerPhpEngine($app,$resolver)
    {
        $resolver->register('php', function() { return new \Illuminate\View\Engines\PhpEngine; });
    }

    /**
     * Register the Blade engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    protected function registerBladeEngine($app,$resolver)
    {
        $resolver->register('blade', function() use ($app)
        {
            return new \Illuminate\View\Engines\CompilerEngine($app['Illuminate.blade'], $app['Illuminate.files']);
        });
    }

    protected function registerTwigEngine($app,$resolver)
    {     
        $resolver->register('twig',function() use($app){ return new \Unika\Ext\TwigEngine($app['twig']); });
    }

    protected function registerFactory($app)
    {
        $app['view.factory'] = function($app)
        {
            // Next we need to grab the engine resolver instance that will be used by the
            // environment. The resolver will be used by an environment to get each of
            // the various engine implementations such as plain PHP or Blade engine.
            $resolver = $app['view.resolver'];

            $finder = $app['view.finder'];

            $factory = new \Unika\Ext\ViewFactory($resolver, $finder, $app['Illuminate.events']);
            //$factory->addExtension('twig','twig');
            // We will also set the container instance on this view environment since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $factory->setContainer($app);

            $factory->share('app', $app);

            return $factory;
        };
    }
}
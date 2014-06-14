<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * modified by Fajar Khairil
 */

namespace Unika\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Twig integration for Silex.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TwigServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['twig.path'] = $app['config']['view.paths'];

        $app['twig.options'] = array(
            'charset'       => $app['config']['app.charset'],
            'debug'         => $app['config']['app.debug'],
            'cache'         => $app['config']['view.twig.cache']
        );  

        $app['twig.form.templates'] = array('form_div_layout.html.twig');
        $app['twig.templates'] = array();

        $app['twig'] = function ($app) {
            $app['twig.options'] = array_replace(
                array(
                    'charset'          => isset($app['charset']) ? $app['charset'] : 'UTF-8',
                    'debug'            => isset($app['debug']) ? $app['debug'] : false,
                    'strict_variables' => isset($app['debug']) ? $app['debug'] : false,
                ), $app['twig.options']
            );

            $twig = new \Twig_Environment($app['twig.loader'], $app['twig.options']);
            $twig->addGlobal('app', $app);

            if (isset($app['debug']) && $app['debug']) {
                $twig->addExtension(new \Twig_Extension_Debug());
            }

            return $twig;
        };

        $app['twig.loader.array'] = function ($app) {
            return new \Twig_Loader_Array($app['twig.templates']);
        };

        $app['twig.loader'] = $app['view.finder'];

        $app['twig.string'] = function ($app) {
            $loader = new \Twig_Loader_String();
            return new \Twig_Environment($loader, $app['twig.options']);          
        };             
    }
}
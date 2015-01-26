<?php

/**
 *  This file is part of the Unika-CMF project
 *  modify default silex  TranslationServiceProvider
 *
 *  @license MIT
 *  @author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Unika\Ext\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\PhpFileLoader;

class TranslationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['translator'] = function ($app) {
            if (!isset($app['locale'])) {
                throw new \LogicException('You must register the LocaleServiceProvider to use the TranslationServiceProvider');
            }

            $translator = new Translator($app, $app['translator.message_selector'],$app['path.base'].'/lang/cache');
            $translator->setFallbackLocales($app['locale_fallbacks']);
            $translator->addLoader('array', new ArrayLoader());
            $translator->addLoader('phpfile', new PhpFileLoader());

            // @todo : optimze it
            // adding all translation resource
            $finder = $app['sf.finder'];
            $resources = $finder->files()->name('*.php')->in($app['path.base'].'/lang');
            foreach( $resources as $res )
            {
                $tmp = $res->getRelativePathname();
                
                if( '' !== $domain = substr($tmp, 0, strpos($tmp, '/')) )
                {
                    $translator->addResource('phpfile',$res->getPathname(),substr( substr($tmp, strpos($tmp, '/')+1) , 0,2),$domain);
                    continue;
                }

                $translator->addResource('phpfile',$res->getPathname(),substr($tmp, 0, strpos($tmp, '_')),'messages');
            }

            return $translator;
        };

        $app['translator.message_selector'] = function () {
            return new MessageSelector();
        };

        $app['translator.domains'] = array();
        $app['locale_fallbacks'] = array('en');
    }
}

<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * modified by Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Provider;

use Pimple\Container;
use Unika\Interfaces\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Provider\Session\SessionListener;
use Silex\Provider\Session\TestSessionListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Symfony HttpFoundation component Provider for sessions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SessionServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    private $app;

    // @todo : respect sessions config
    public function register(Container $app)
    {
        $this->app = $app;

        $app['session.test'] = false;

        $app['session'] = function ($app) {
            if (!isset($app['session.storage'])) {
                if ($app['session.test']) {
                    $app['session.storage'] = $app['session.storage.test'];
                } else {
                    $app['session.storage'] = $app['session.storage.native'];
                }
            }

            return new Session($app['session.storage']);
        };

        $app['session.storage.handler'] = function ($app) {
            return new NativeFileSessionHandler($app['path.var'].'/sessions');
        };

        $app['session.storage.native'] = function ($app) {
            return new NativeSessionStorage(
                $app['session.storage.options'],
                $app['session.storage.handler']
            );
        };

        $app['session.listener'] = function ($app) {
            return new SessionListener($app);
        };

        $app['session.storage.test'] = function () {
            return new MockFileSessionStorage();
        };

        $app['session.listener.test'] = function ($app) {
            return new TestSessionListener($app);
        };

        $app['session.storage.options'] = array('name' => $app->config('sessions.drivers.file.name'));
        $app['session.default_locale'] = 'en';
        $app['session.storage.save_path'] = null;
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['session.listener']);

        if ($app['session.test']) {
            $app['dispatcher']->addSubscriber($app['session.listener.test']);
        }
    }

    /**
     *
     *  return description of provider
     */
    public function getDescription()
    {
        return 'Session Service Provider';
    }

    /**
     *
     *  return array of service with each description
     */
    public function getServices()
    {
        return array(
            'session'  => 'Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage'
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
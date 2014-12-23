<?php
/**
 * This file is part of the UnikaCMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

namespace Unika\Provider;

use RuntimeException;
use Silex\Application;
use Unika\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Whoops\Handler\Handler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class WhoopsServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(\Pimple\Container $app)
    {
        // There's only ever going to be one error page...right?
        $app['whoops.error_page_handler'] = function () {
            if (PHP_SAPI === 'cli') {
                return new PlainTextHandler();
            } else {
                return new PrettyPageHandler();
            }
        };

        // Retrieves info on the Silex environment and ships it off
        // to the PrettyPageHandler's data tables:
        // This works by adding a new handler to the stack that runs
        // before the error page, retrieving the shared page handler
        // instance, and working with it to add new data tables
        $app['whoops.silex_info_handler'] = $app->protect(function () use ($app) {
            try {
                /** @var Request $request */
                $request = Request::createFromGlobals();
            } catch (RuntimeException $e) {
                // This error occurred too early in the application's life
                // and the request instance is not yet available.
                return;
            }

            /** @var Handler $errorPageHandler */
            $errorPageHandler = $app["whoops.error_page_handler"];

            if ($errorPageHandler instanceof PrettyPageHandler) {
                /** @var PrettyPageHandler $errorPageHandler */

                // General application info:
                $errorPageHandler->addDataTable('Silex Application', array(
                    'Charset'          => $app['charset'],
                    'Locale'           => 'en_us',
                    'Route Class'      => $app['route_class'],
                    'Dispatcher Class' => $app['dispatcher_class'],
                    'Application Class' => get_class($app),
                ));

                // Request info:
                $errorPageHandler->addDataTable('Silex Application (Request)', array(
                    'URI'         => $request->getUri(),
                    'Request URI' => $request->getRequestUri(),
                    'Path Info'   => $request->getPathInfo(),
                    'Query String' => $request->getQueryString() ?: '<none>',
                    'HTTP Method' => $request->getMethod(),
                    'Script Name' => $request->getScriptName(),
                    'Base Path'   => $request->getBasePath(),
                    'Base URL'    => $request->getBaseUrl(),
                    'Scheme'      => $request->getScheme(),
                    'Port'        => $request->getPort(),
                    'Host'        => $request->getHost(),
                ));
            }
        });

        $app['whoops'] = function () use ($app) {
            $run = new Run();
            $run->allowQuit(false);

            $run->pushHandler($app['whoops.error_page_handler']);
            $run->pushHandler($app['whoops.silex_info_handler']);

            $jsonHandler = new \Unika\Ext\Whoops\JsonResponseHandler();
            $jsonHandler->onlyForAjaxRequests(True);
            $run->pushHandler($jsonHandler);
            
            return $run;
        };

        $app['whoops']->register();
    }

    /**
     *
     *  return description of provider
     */
    public function getDescription()
    {
        return 'Whoops Service Provider php error for cool kids';
    }

    /**
     *
     *  return array of service with each description
     */
    public function getServices()
    {
        return array(
            'whoops'  => 'Whoops\Run'
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
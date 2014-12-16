<?php
/**
 *  This file is part of the Unika-CMF project
 *  
 *  @license MIT
 *  @author Fajar Khairil <fajar.khairil@gmail.com>
 */

$app = require_once 'bootstrap.php';

require_once 'routes.php';

//define Application error handler
$app->error(function ($e,$request) use ($app) 
{
    $code = $e instanceof HttpException ? $e->getStatusCode() : 500;
    $app['logger']->addError($e->getMessage().' : '.$code);
    
    if( $app['debug'] )
    {
        if( !extension_loaded('xdebug') )
        {
            $method = \Whoops\Run::EXCEPTION_HANDLER;
            ob_start();
            $app['whoops']->$method($e);
            $response = ob_get_clean();
            
            return new \Symfony\Component\HttpFoundation\Response($response, $code);
        }
        else
        {
            throw $e;
        }
    }
    else
    {
        return new \Symfony\Component\HttpFoundation\Response('Ooops.. something went terribly wrong.', $code);
    }
});

$app->run();
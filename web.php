<?php
/**
 *  This file is part of the Unika-CMF project
 *  
 *  @license MIT
 *  @author Fajar Khairil <fajar.khairil@gmail.com>
 */

$app = require_once 'bootstrap.php';

/**
 * managing Asset Path
 */
$app->before(function ($request, $app) {

    //or it can be yor own cdn url!!
    $app['asset_path'] = $request->getBasePath();
    $app['stylesheets'] = $app['asset_path'].'/css/';
    $app['scripts'] = $app['asset_path'].'/js/';
    $app['img'] = $app['asset_path'].'/img/';

});

/** 
 *  Generic before middleware, you can calways alter it
 */
$app->before(function ($request,$app){
    $app['view']->share( 'page_title',$app->config('app.name').' &mdash; ');
});

require_once 'routes.php';

/**
 * define Application error handler
 */
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
       throw $e;
    }
});

$app->run();
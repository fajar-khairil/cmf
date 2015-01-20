<?php
/**
 *  This file is part of the Unika-CMF project
 *  
 *  @license MIT
 *  @author Fajar Khairil <fajar.khairil@gmail.com>
 */

$app = require_once 'bootstrap.php';

/** 
 *  Generic before middleware, you can calways alter it
 */
$app->before(function ($request,$app){
    // asset path or it can be yor own cdn url!!
    $app['asset_path'] = $request->getBasePath();
    $app['stylesheets'] = $app['asset_path'].'/css/';
    $app['scripts'] = $app['asset_path'].'/js/';
    $app['img'] = $app['asset_path'].'/img/';

    $app['view']->share( 'page_title',$app->config('app.name').' &mdash; ');
});

$app->before(function ($request,$app){
    $uris = $app->convertRequestUri($request->getRequestUri());
    //dd('request.'.$uris['controller'].'.'.$uris['action']);
    $app['Illuminate.events']->fire('request.'.$uris['controller'].'.'.$uris['action'],array($request,$app));
},-512);

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
        dump($e);
    }
    else
    {
       throw $e;
    }
});

$app->run();
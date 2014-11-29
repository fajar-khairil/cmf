<?php
require_once '../vendor/autoload.php';

use Unika\Application as Application;

//define the root directory
Application::$ROOT_DIR = realpath('../');

$environtments = array(
    'local' => array(
        'fajardev'
    ),
    'staging'   =>  array(
        'staging-machine'
    )
);

foreach( $environtments as $env=>$machine )
{
	if( in_array(gethostname(),$machine) )
	{
		Application::$ENVIRONMENT = $env;
		break;
	}
}

$app = new Application();

require_once 'routes.php';

$app->error(function ($e,$request) use ($app) 
{
    if( $app['debug'] )
    {
        $method = \Whoops\Run::EXCEPTION_HANDLER;
        ob_start();
        $app['whoops']->$method($e);
        $response = ob_get_clean();
        $code = $e instanceof HttpException ? $e->getStatusCode() : 500;

        return new \Symfony\Component\HttpFoundation\Response($response, $code);
    }
    else
    {
        return new \Symfony\Component\HttpFoundation\Response('Something Went Wrong', $code);
    }
});

$app->run();
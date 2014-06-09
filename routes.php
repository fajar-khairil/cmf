<?php

$app = Application::instance();

$app->get('/',function() use($app){			
	$auth = $app['auth'];
	if( $auth->check() )
	{
		return 'Hello World!';
	}
	else
	{
		return 'Sorry you dont have permission';
	}
});

//mounting Backend URI
$app->mount('/'.$app['config']['app.backend_uri'],new \Unika\Provider\BackendControllerProvider());
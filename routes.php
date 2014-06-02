<?php

$app = Application::instance();

$app->get('/',function() use($app){		
	$auth = new \Unika\Security\Eloquent\Auth($app);
	dd( $auth->check() );
	return '<p>Hello World</p>';
});

//mounting Backend URI
$app->mount('/'.$app['config']['app.backend_uri'],new Controller_BackendControllerProvider());
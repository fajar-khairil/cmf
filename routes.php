<?php

$app = Application::instance();

$app->get('/',function() use($app){		
	dd($app['request']->cookies->get('_remember'));
	return '<p>Hello World</p>';
});

//mounting Backend URI
$app->mount('/'.$app['config']['app.backend_uri'],new \Unika\Provider\BackendControllerProvider());
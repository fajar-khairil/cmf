<?php

$app = \Unika\Bag::instance();

$app->get('/',function() use($app){		
	return '<p>Hello World</p>';
});

//mounting Backend URI
$app->mount('/'.$app['config']['app.backend_uri'],new \Unika\Controller\BackendControllerProvider());
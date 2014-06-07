<?php

$app = Application::instance();

$app->get('/',function() use($app){			
	return \utilphp\util::var_dump_plain($app['session']);
});

//mounting Backend URI
$app->mount('/'.$app['config']['app.backend_uri'],new \Unika\Provider\BackendControllerProvider());
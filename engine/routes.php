<?php

$app->get('/',function() use($app) {
	return 'Current Environtment : '.$app->detectEnvirontment().'<br>'.$app->url('backend').'<br>'.$app['config']['app.debug'];
});

//mounting Backend URI
$app->mount('/'.$app['backend_uri'],new \Unika\BackendControllerProvider());
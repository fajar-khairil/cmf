<?php

$app->get('/',function() use($app) {
	return \Ohanzee\Helper\Arr::path(array('one' => 'satu','two' => 'dua'),'two');
});

//mounting Backend URI
$app->mount('/'.$app['config']['app.backend_uri'],new \Unika\Controller\BackendControllerProvider());
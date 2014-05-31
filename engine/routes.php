<?php

$app->get('/',function() use($app) {
	return $app['Kohana.text']->censor('lo boker semabarangan aja tai!',array('tai','boker'));
});

//mounting Backend URI
$app->mount('/'.$app['config']['backend_uri'],new \Unika\BackendControllerProvider());
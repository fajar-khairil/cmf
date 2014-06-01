<?php

$app = \Unika\Bag::instance();

$app->get('/',function() use($app){
	/*$crequest = $app['request'];
	dd(get_class($crequest));*/

	$auth = new \Unika\Security\Eloquent\Auth();

	if( $auth->attempt(['username' => 'sysadmin','pass' => 'masterkey'],True,5) )
	{
		return 'successfully loggedin';
	}
	else
	{
		return 'failed to loggedin';
	}
	
	return 'Hello World';
});

//mounting Backend URI
$app->mount('/'.$app['config']['app.backend_uri'],new \Unika\Controller\BackendControllerProvider());
<?php

$app->get('/',function() use($app) {
	$Capsule = $app['Capsule'];
	$users = $Capsule::table('sessions')->where('session_id', '=', 'cmvpbnpfctdhjkiahpkclvq6s1')->get();
	
	$tmp = '';
	foreach( $users as $user )
	{
		$tmp .= $user['session_id'].'<br>';
	}

	return $tmp;
});

//mounting Backend URI
$app->mount('/'.$app['backend_uri'],new \Unika\BackendControllerProvider());
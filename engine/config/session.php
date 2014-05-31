<?php
return array(
	'Native' => array(
		'path' 	=> '../tmp/sessions'
	),
	'Database' => array(
		'dsn'		=> 'mysql:dbname=cmf',
		'user'		=> 'root',
		'password'	=> 'masterkey',
		'table' 	=> 'sessions'
	),
	//not yet implemented let it blank
	'Memcached'	=> array(

	)
);
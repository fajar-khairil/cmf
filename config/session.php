<?php
//supported session.handler ['Database','File','Mongodb','Memcached']
return array(
    //valid File/Database
    'default'		=> 'Database', 
	'File' => array(
		'path' 	=> '../tmp/sessions'
	),
	'Database' => array(
		'dsn'			=> 'mysql:dbname=cmf',
		'user'			=> 'root',
		'password'		=> 'masterkey',
		'table' 		=> 'sessions',
		'session_info'	=> array(
			'enabled'	=> False,
			'table'		=> 'session_info'
		)
	),
	//depending on your installed extending it can be Memcached or Memcache , Memcached take first priority
	'Memcached'	=> array(
		'prefix'		=> 'cmf_',
		'expiretime'	=> 600
	),
	'Mongodb'	=>	array(
		'database' 		=> 'sessions',
		'collection' 	=> 'sess_collection',
		'id_field'		=> 'sess_id',
		'data_field'	=> 'sess_content',
		'time_field'	=> 'sess_time'
	)
);
<?php
//supported session.handler ['Database','File','Mongodb','Memcached']
return array(
    //valid File/Database
    'default'		=> 'Database', 
	'File' => array(
		'path' 	=> \Application::$ENGINE_PATH.'/tmp/sessions'
	),
	'Database' => array(
		'dsn'			=> 'mysql:dbname=cmf',
		'user'			=> 'root',
		'password'		=> 'masterkey',
		'table' 		=> 'sessions',
		'session_info'	=> array(
			'table'		=> 'session_info'
		)
	),
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
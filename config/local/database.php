<?php
return array(
	'default'	=> 'master',//default connection
	'master'	=>	array(
		'driver'    => 'mysql',
		'host'      => 'localhost',
		'database'  => 'cmf',
		'username'  => 'root',
		'password'  => 'masterkey',

                'id_column' => 'id',
                'id_column_overrides' => array(),
                'error_mode' => PDO::ERRMODE_EXCEPTION,
                'driver_options' => null,
                'identifier_quote_character' => null, // if this is null, will be autodetected
                'limit_clause_style' => null, // if this is null, will be autodetected
                'logging' => false,
                'caching' => false,
                'cache_expiration' => 5, // in minutes
                'cache_driver' => 'File' // refer to config cache
	)
);
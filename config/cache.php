<?php
return array(
	'driver'	=>	'File',
	'prefix'	=> 'cache_',
	'path' => \Unika\Application::$ROOT_DIR.'/var/cache',

	'File'	=>	array(
		'expired'	=> 5, // in minutes
	),
	'Array', // ArrayCache has no options
	'Null', // NullCache has no options
	'Memcached'	=>	array(
		['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100],
	)
);
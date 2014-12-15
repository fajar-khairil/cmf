<?php
return array(
	'default'	=>	'File',
	'prefix'	=> 'cache_',
	'File'	=>	array(
		'dir'		=> \Unika\Application::$ROOT_DIR.'/var/cache',
		'prefix'	=> 'cache_',
		'expired'	=> 5, // in minutes
	),
	'Array', // ArrayCache has no options
	'Null', // NullCache has no options
	'Memcached'	=>	array(
		['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100],
	),
	'Redis'	=>	array(
		'cluster' => false,

		'default' => [
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		],
	)
);
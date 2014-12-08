<?php
return array(
	'default'	=>	'File',
	'prefix'	=> 'cache_',
	'File'	=>	array(
		'dir'		=> \Unika\Application::$ROOT_DIR.'/tmp/cache',
		'prefix'	=> 'cache_',
		'expired'	=> 5, // in minutes
	),
	'Array', // ArrayCache has no options
	'Null', // NullCache has no options
);
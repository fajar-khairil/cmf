<?php
return array(
	//valid Apc,Memcached,File,Array,Redis
    'default'   => 'Apc',
	'prefix'	=> 'cmf_',
	'File' 		=> array(
		'path'	=> Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'cache'
	),
	'Memcached'	=> array(
		array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100)
	)
);
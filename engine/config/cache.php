<?php
return array(
	'prefix'	=> 'cmf_',
	'File' 		=> array(
		'path'	=> '../tmp/cache'
	),
	'Memcached'	=> array(
		array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100)
	)
);
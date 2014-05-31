<?php
return array(
	'prefix'	=> 'cmf_',
	'File' 		=> array(
		'path'	=> \Unika\Bag::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'cache'
	),
	'Memcached'	=> array(
		array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100)
	)
);
<?php
return array(
	'prefix'	=> 'cmf_',
	'File' 		=> array(
		'path'	=> '../tmp/cache'
	),
	'Memcached'	=> array(
			  //host,port,weight
	    array('mem1.domain.com', 11211, 33),
	    array('mem2.domain.com', 11211, 67)
	)
);
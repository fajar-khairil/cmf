<?php
return array(
	'default'	=>	'file',
	'drivers'	=> array(
		'file'	=>	array(
			'name'		=>	'UNIKA_SESS',
			'lifetime'	=>	1800,// 3oMin
			'domain'	=> $_SERVER['HTTP_HOST'],
			'secure'	=> False,
			'httponly'	=> True
		)
	)
);